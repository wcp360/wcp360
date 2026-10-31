"""Real HTTP integration against the standalone Symfony kernel. Uses only Python stdlib.
Never runs against existing application data: makes an isolated temporary copy.
"""
import contextlib, hashlib, http.cookiejar, json, os, re, shutil, socket, subprocess, tempfile, time
from pathlib import Path
from urllib.request import build_opener, HTTPCookieProcessor, Request
from urllib.error import HTTPError
from urllib.parse import urlencode
source=Path(__file__).resolve().parents[1]
with tempfile.TemporaryDirectory(prefix="fm-http-") as tmp:
    p=Path(tmp)/"app"
    shutil.copytree(source,p,ignore=shutil.ignore_patterns("vendor","var",".git",".phpunit.cache"))
    (p/"vendor").symlink_to(source/"vendor",target_is_directory=True)
    v=p/"standalone/var"; (v/"files").mkdir(parents=True); (v/"state").mkdir()
    password_hash=subprocess.check_output(["php","-r","echo password_hash('integration-password',PASSWORD_DEFAULT);"]).decode()
    config="<?php return "+"[\n"+"'APP_SECRET'=>'"+os.urandom(32).hex()+"',\n'FM_USERNAME'=>'tester',\n'FM_PASSWORD_HASH'=>'"+password_hash+"',\n'FM_ROOT'=>__DIR__.'/files',\n'FM_STATE'=>__DIR__.'/state'];"
    (v/"config.php").write_text(config)
    with socket.socket() as sock: sock.bind(("127.0.0.1",0)); port=sock.getsockname()[1]
    base=f"http://127.0.0.1:{port}"
    log=open(Path(tmp)/"server.log","w+")
    server=subprocess.Popen(["php","-d","upload_max_filesize=20M","-d","post_max_size=22M","-S",f"127.0.0.1:{port}","-t","standalone/public","standalone/public/index.php"],cwd=p,stdout=log,stderr=log)
    client=build_opener(HTTPCookieProcessor(http.cookiejar.CookieJar()))
    checks=[]
    def check(name,condition):
        assert condition,name
        checks.append(name)
    def req(path,data=None,headers=None):
        try: r=client.open(Request(base+path,data=data,headers=headers or {}),timeout=15)
        except HTTPError as e: r=e
        return r,r.read()
    def call(op,data=None,token=True):
        headers={"Content-Type":"application/json"}
        if token: headers["X-CSRF-Token"]=csrf
        r,b=req("/api/v1/files/"+op,json.dumps(data).encode() if data is not None else None,headers)
        return r,json.loads(b)
    try:
        for i in range(100):
            try: r,b=req("/login"); break
            except OSError: time.sleep(.1)
        check("login page",r.status==200 and b"_csrf_token" in b)
        r,body=req("/api/v1/files/list?path=")
        check("anonymous cannot read",r.geturl().endswith("/login"))
        token=re.search(rb'name="_csrf_token" value="([^"]+)"',b).group(1).decode()
        r,b=req("/login",urlencode({"_username":"tester","_password":"integration-password","_csrf_token":token}).encode(),{"Content-Type":"application/x-www-form-urlencoded"})
        check("login succeeds",r.status==200 and b"Mes fichiers" in b)
        r,b=req("/api/v1/files/session"); session=json.loads(b)["data"]; csrf=session["csrf"]
        check("identity",session["user"]=="tester" and session["can_write"])
        r,b=call("create",{"path":"a.txt","content":"bonjour"},False)
        check("CSRF required",r.status==403 and b["error"]["code"]=="CSRF_INVALID")
        r,b=call("create",{"path":"a.txt","content":"bonjour"}); revision=b["data"]["revision"]
        check("create",r.status==200)
        r,b=call("read?path=a.txt"); check("read",b["data"]["content"]=="bonjour")
        r,b=call("save",{"path":"a.txt","content":"salut","revision":revision}); check("save",r.status==200)
        r,b=call("save",{"path":"a.txt","content":"perdu","revision":revision}); check("stale edit rejected",r.status==409)
        r,b=call("create",{"path":"../escape.txt","content":"x"}); check("traversal rejected",r.status==400)
        r,b=req("/api/v1/files/save",b"{bad",{"Content-Type":"application/json","X-CSRF-Token":csrf});check("malformed JSON",r.status==400)
        r,b=req("/api/v1/files/save"); check("wrong method",r.status==405)
        r,b=req("/api/v1/files/download?path=a.txt");check("download",b==b"salut" and r.headers["Content-Disposition"].startswith("attachment"))
        check("no cache and nosniff","no-store" in r.headers["Cache-Control"] and r.headers["X-Content-Type-Options"]=="nosniff")
        boundary="fmtestboundary"; payload=("--"+boundary+"\r\nContent-Disposition: form-data; name=\"path\"\r\n\r\nimport.txt\r\n--"+boundary+"\r\nContent-Disposition: form-data; name=\"file\"; filename=\"import.txt\"\r\nContent-Type: text/plain\r\n\r\nimported\r\n--"+boundary+"--\r\n").encode()
        r,b=req("/api/v1/files/upload",payload,{"Content-Type":"multipart/form-data; boundary="+boundary,"X-CSRF-Token":csrf});check("multipart upload",r.status==200)
        r,b=call("trash",{"path":"a.txt","revision":hashlib.sha256(b"salut").hexdigest()}); check("trash",r.status==200); ident=b["data"]["id"]
        r,b=call("restore",{"id":ident});check("restore",r.status==200)
        r,b=req("/file-manager/assets/app.js");check("javascript asset",r.status==200 and b"async function api" in b)
        r,b=call("properties?path=a.txt");check("properties and owner metadata",r.status==200 and "uid" in b["data"] and "content" not in b["data"])
        r,b=call("mkdir",{"path":"folder"});check("create folder",r.status==200)
        r,b=call("create",{"path":"folder/nested.txt","content":"nested"});check("nested file",r.status==200)
        r,b=call("copy",{"path":"folder","destination":"folder-copy"});check("recursive copy API",r.status==200 and b["data"]["entries"]==2)
        r,b=call("copy",{"path":"folder","destination":"folder-copy"});check("copy collision API",r.status==409)
        r,b=call("copy",{"path":"folder","destination":"folder/inside"});check("copy descendant API",r.status==400)
        r,b=call("create",{"path":".env","content":"APP=demo"})
        r,b=call("list?path=&hidden=0");check("hide dotfiles API",all(not x["name"].startswith(".") for x in b["data"]["items"]))
        r,b=call("list?path=&directories=1");check("directory tree API",all(x["kind"]=="directory" for x in b["data"]["items"]) and b["data"]["total"]==2)
        r,b=call("trash",{"path":"folder-copy"});check("nonempty trash API",r.status==200); folder_id=b["data"]["id"]
        r,b=call("restore",{"id":folder_id});check("nonempty restore API",r.status==200)
        r,b=call("read?path=folder-copy/nested.txt");check("restored nested bytes",b["data"]["content"]=="nested")
        r,b=call("capabilities");check("admin capabilities",b["data"]["admin"] and b["data"]["chmod"])
        r,b=call("disk_usage?path=folder");check("disk analysis",b["data"]["analysis_path"]=="folder" and "largest" in b["data"])
        r,b=call("chmod",{"path":"a.txt","mode":"640"});check("chmod API",r.status==200 and b["data"]["changed"]==1)
        r,b=call("chmod",{"path":"a.txt","mode":"777"});check("unsafe permissions rejected",r.status==403)
        r,b=call("search",{"path":"","query":"nested"});check("content search API",len(b["data"]["matches"])==2)
        r,b=call("versions?path=a.txt");check("version history",len(b["data"]["items"])==1)
        r,b=call("lock_acquire",{"path":"a.txt"});check("lock acquire API",r.status==200); lock=b["data"]["token"]
        r,b=call("save",{"path":"a.txt","content":"blocked","revision":hashlib.sha256(b"salut").hexdigest()});check("locked mutation rejected",r.status==423)
        r,b=call("lock_release",{"path":"a.txt","lock_token":lock});check("lock release API",r.status==200)
        r,b=call("share_create",{"path":"a.txt","password":"integration-share-password","ttl":3600});check("share creation",r.status==200); shared=b["data"]
        guest=build_opener()
        response=guest.open(Request(base+shared["url_path"],data=urlencode({"password":"integration-share-password"}).encode(),headers={"Content-Type":"application/x-www-form-urlencoded"}));check("anonymous protected download",response.read()==b"salut")
        r,b=call("share_revoke",{"id":shared["id"]});check("share revoked",r.status==200)
        r,b=call("audit");check("audit path and IP",any(i.get("path")=="a.txt" and i.get("ip")=="127.0.0.1" for i in b["data"]["items"]))
        check("audit redacts credentials", "integration-share-password" not in json.dumps(b) and shared["token"] not in json.dumps(b))
        r,b=req("/api/v1/files/snapshot",json.dumps({"path":"folder"}).encode(),{"Content-Type":"application/json","X-CSRF-Token":csrf});check("snapshot API attachment",r.status==200 and b.startswith(b"PK") and r.headers["Content-Disposition"].startswith("attachment"))
        r,b=req("/logout",urlencode({"_csrf_token":session["logout_csrf"]}).encode(),{"Content-Type":"application/x-www-form-urlencoded"});check("logout",r.geturl().endswith("/login"))
        r,b=req("/api/v1/files/session");check("session revoked",r.geturl().endswith("/login"))
        print(json.dumps({"status":"PASS","count":len(checks),"checks":checks},indent=2))
    except Exception:
        log.flush(); log.seek(0); print(log.read()[-12000:]); print("RESPONSE",r.status,b[:1500]);
        for f in (v/"log").glob("*"): print(f.read_text()[-8000:]);
        for f in (v/"logs").glob("*"): print(f.read_text()[-8000:]);
        raise
    finally:
        server.terminate();server.wait(timeout=10);log.close()
