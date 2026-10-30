"""Setup is tested in isolation; never print the generated password."""
from pathlib import Path
import tempfile, shutil, subprocess, json, re
source=Path(__file__).resolve().parents[1]
with tempfile.TemporaryDirectory(prefix='fm-setup-') as tmp:
    app=Path(tmp)/'with spaces'
    (app/'standalone/bin').mkdir(parents=True)
    shutil.copy(source/'standalone/bin/setup.php',app/'standalone/bin/setup.php')
    r=subprocess.run(['php',str(app/'standalone/bin/setup.php')],input='tester\n',text=True,capture_output=True)
    assert r.returncode==0, 'setup failed'
    config=app/'standalone/var/config.php'
    assert config.exists()
    password=re.search(r'\n([a-f0-9]{32})\n',r.stdout).group(1)
    # Verify hash without exposing generated credentials in command-line arguments.
    script="$c=require $argv[1]; echo json_encode(['user'=>$c['FM_USERNAME'],'valid'=>password_verify(trim(stream_get_contents(STDIN)),$c['FM_PASSWORD_HASH']),'root'=>$c['FM_ROOT']]);"
    d=json.loads(subprocess.check_output(['php','-r',script,str(config)],input=password.encode()))
    assert d['user']=='tester' and d['valid'] and Path(d['root']).is_dir()
    before=config.read_bytes()
    again=subprocess.run(['php',str(app/'standalone/bin/setup.php')],input='overwrite\n',text=True,capture_output=True)
    assert again.returncode!=0 and config.read_bytes()==before, 'existing config must be preserved'
    moved=Path(tmp)/'moved'; app.rename(moved)
    d=json.loads(subprocess.check_output(['php','-r',script,str(moved/'standalone/var/config.php')],input=password.encode()))
    assert Path(d['root'])==moved/'standalone/var/files' and d['valid']
    print(json.dumps({'status':'PASS','checks':['setup generation','generated password verifies','existing setup protected','space in path','relocation safe']}))
