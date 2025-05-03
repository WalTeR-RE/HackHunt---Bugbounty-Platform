
async function authenticated(){
    let check = true;
    const access_token = localStorage.getItem("token");
    if (!access_token) {
        check = false;
    }
    const res1 = await fetch("http://127.0.0.1:8000/api/auth/me", {
        headers: {
            "Authorization": `Bearer ${access_token}`
            }
        });
    
    const user = await res1.json();
        if(user.success === false) {
            check = false;
        }
    if(check === false) {
    if(await try_refresh_access_token() === false) {
       check = false;
    }
}

    return check;
}

async function try_refresh_access_token() {
    const acc_old = localStorage.getItem("token");
    const access_token = localStorage.getItem("refresh_token");
    if (!access_token) {
        return false;
    }
    const res1 = await fetch("http://127.0.0.1:8000/api/auth/refresh", {
        method: "POST",
        headers: {
            "Authorization": `Bearer ${acc_old}`,
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            "refresh_token": access_token
        })
    });
    const data = await res1.json();
    if (data.success === true) {
        localStorage.setItem("token", data.token['access_token']);
        localStorage.setItem("refresh_token", data.token['refresh_token']);
        return true;       
    }

    return false;
}