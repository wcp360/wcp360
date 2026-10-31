/* Reusable browser-local resource/job reconciliation. No external operations. */
((global) => {
  'use strict';
  const clone = value => structuredClone(value);
  const stamp = value => JSON.stringify(value, (_key, item) => item && typeof item === 'object' && !Array.isArray(item) ? Object.fromEntries(Object.keys(item).sort().map(key => [key, item[key]])) : item);
  const terminal = status => ['completed', 'failed', 'cancelled'].includes(status);

  function create({namespace, scope, read, write, recipes, owned = () => true}) {
    function load() {
      const state = read();
      if (state.resourceOperations === undefined) state.resourceOperations = [];
      if (!Array.isArray(state.resourceOperations)) throw new Error('Resource operation storage is invalid.');
      return state;
    }
    const recipeFor = action => {
      const recipe = Object.hasOwn(recipes, action) && recipes[action];
      if (!recipe) throw new Error('Unknown resource operation.');
      return recipe;
    };
    const rows = (state, recipe) => {
      if (!Array.isArray(state[recipe.collection])) throw new Error('Resource inventory is unavailable.');
      return state[recipe.collection];
    };
    function release(state, operation, recipe, result, failed = true) {
      const row = rows(state, recipe).find(row => row.id === operation.resourceId && owned(row, recipe.collection));
      if (!row || row._pending?.operationId !== operation.id) return;
      const field = recipe.statusField || 'status';
      if (row[field] === recipe.pending) row[field] = operation.effect === 'create' ? (result.includes('cancelled') ? 'Provisioning cancelled' : 'Provisioning failed') : operation.before[field];
      delete row._pending;
      row.lifecycleResult = {operationId: operation.id, status: failed ? 'failed' : 'completed', result, jobId: operation.jobId};
    }
    function sync() {
      const state = load(), jobs = global.WCPRuntime?.getState().jobs || [];
      let changed = false;
      for (const operation of state.resourceOperations) {
        if (!operation || operation.namespace !== namespace || operation.scope !== scope || operation.applied) continue;
        const recipe = recipeFor(operation.action), job = jobs.find(job => job.id === operation.jobId && job.scope === scope && job.kind === namespace + '-' + operation.action);
        if (!job) continue;
        if (operation.status !== job.status || operation.progress !== job.progress) {
          operation.status = job.status; operation.progress = job.progress; changed = true;
        }
        if (!terminal(job.status)) continue;
        operation.applied = true; operation.completedAt = job.completedAt || Date.now(); changed = true;
        const current = rows(state, recipe).find(row => row.id === operation.resourceId && owned(row, recipe.collection));
        let problem = job.status !== 'completed' ? 'No change: job ' + job.status : operation.payload.health === 'Fail' ? 'Simulated operation failed' : '';
        if (!problem && (!current || rows(state, recipe).filter(row => row.id === operation.resourceId).length !== 1 || current._pending?.operationId !== operation.id || stamp(current) !== operation.expected)) problem = 'Stale: resource changed while the operation was pending';
        if (!problem && recipe.context && stamp(recipe.context(state, operation.payload)) !== operation.context) problem = 'Stale: source or governing inventory changed';
        if (!problem) {
          try {
            const prepared = recipe.prepare(state, clone(operation.before), clone(operation.payload), operation.resourceId);
            if (stamp(prepared) !== stamp(operation.after)) throw new Error('The reviewed result changed');
          } catch (error) { problem = 'Blocked: ' + error.message; }
        }
        if (problem) { operation.result = problem; release(state, operation, recipe, problem); continue; }
        if (recipe.effect === 'remove') state[recipe.collection] = rows(state, recipe).filter(row => row.id !== operation.resourceId);
        else {
          const after = clone(operation.after); delete after._pending;
          after.lifecycleResult = {operationId: operation.id, status: 'completed', result: 'Applied demo ' + operation.action, jobId: operation.jobId};
          state[recipe.collection] = rows(state, recipe).map(row => row.id === operation.resourceId ? after : row);
        }
        recipe.applied?.(state, operation);
        operation.result = 'Applied demo ' + operation.action;
      }
      if (changed) write(state);
      return {state, changed};
    }
    function queue({action, id, payload = {}, expected, retry = false}) {
      if (!global.WCPRuntime) throw new Error('The Job Center is still loading. Try again in a moment.');
      const {state} = sync(), recipe = recipeFor(action), inventory = rows(state, recipe);
      const current = inventory.find(row => row.id === id);
      if (inventory.filter(row => row.id === id).length > 1) throw new Error('Duplicate resource identity in this inventory.');
      if (current && !owned(current, recipe.collection)) throw new Error('This resource is outside the active scope.');
      if (current?._pending || state.resourceOperations.some(operation => operation.namespace === namespace && !operation.applied && operation.collection === recipe.collection && operation.resourceId === id)) throw new Error('A resource operation is already pending.');
      if (expected !== undefined && stamp(current || null) !== expected) throw new Error('The reviewed resource changed. Reopen the form.');
      if (recipe.effect === 'create') {
        if (current && !(retry && recipe.retryable?.(current))) throw new Error('This resource already exists.');
        if (!current && retry) throw new Error('The resource to retry no longer exists.');
      } else if (!current) throw new Error('The resource no longer exists.');
      const input = clone(payload);
      if (!['Pass', 'Fail'].includes(input.health || 'Pass')) throw new Error('Unknown simulated outcome.');
      input.health = input.health || 'Pass';
      input.requestedAt = new Date().toISOString();
      const before = current ? clone(current) : null, after = recipe.prepare(state, before && clone(before), input, id);
      if (recipe.effect !== 'remove' && (!after || after.id !== id || !owned(after, recipe.collection))) throw new Error('Invalid reviewed resource identity.');
      const operationId = global.crypto?.randomUUID?.() || Date.now() + '-' + Math.random();
      const job = global.WCPRuntime.addJob({name: recipe.title + ': ' + (after?.domain || current?.domain || id), kind: namespace + '-' + action, scope, actor: 'Prototype operator', status: 'running', reason: input.reason || ''});
      const pending = clone(recipe.effect === 'create' ? after : current), field = recipe.statusField || 'status';
      pending[field] = recipe.pending; pending._pending = {operationId, jobId: job.id};
      state[recipe.collection] = current ? inventory.map(row => row.id === id ? pending : row) : [pending, ...inventory];
      state.resourceOperations.unshift({id: operationId, namespace, scope, collection: recipe.collection, resourceId: id, action, effect: recipe.effect, payload: input, before, after: clone(after), expected: stamp(pending), context: recipe.context ? stamp(recipe.context(state, input)) : null, jobId: job.id, status: job.status, progress: job.progress, createdAt: new Date().toISOString(), applied: false});
      // Keep every pending operation, plus the most recent completed records.
      const retained = state.resourceOperations.filter(operation => operation.applied).slice(0, 100);
      state.resourceOperations = state.resourceOperations.filter(operation => !operation.applied || retained.includes(operation));
      try { write(state); } catch (error) { try { global.WCPRuntime.cancelJob?.(job.id); } catch {} throw error; }
      return {state, operation: state.resourceOperations[0]};
    }
    const operations = () => load().resourceOperations.filter(operation => operation.namespace === namespace && operation.scope === scope);
    return Object.freeze({sync, queue, read: load, operations});
  }
  global.WCPResourceLifecycle = Object.freeze({create, stamp});
})(globalThis);
