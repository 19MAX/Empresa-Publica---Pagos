function showFlashMessage(text, type) {
    if (type =="danger") {
        background="linear-gradient(to right, #f73154, #ff7f50)";
    }else if(type == "warning"){
        background="linear-gradient(to right, #ffa107, #ffca00)";
    }else if (type == "success") {
        background="linear-gradient(to right, #00b09b, #96c93d)";
    }else{
        background="linear-gradient(to right, #00b09b, #96c93d)";
    }
    Toastify({
        text: text,
        duration: 3000,
        newWindow: true,
        close: true,
        gravity: "top", // `top` or `bottom`
        position: "right", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
            background: background,
        },
    }).showToast();
}