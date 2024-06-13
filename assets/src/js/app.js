document.addEventListener('DOMContentLoaded', function(){
    const form = document.querySelector("#post_form");
    form.addEventListener("submit", function(e){
        e.preventDefault();
        const btn = document.querySelector("#post_form button");
        btn.setAttribute('disabled', true);
        window.tinyMCE.triggerSave();
        const formData = new FormData(e.target);
        const formProps = Object.fromEntries(formData);

        if (formProps.post_title && formProps.post_content) {
            formData.append('action', 'propose_post_form');
            formData.append('nonce', settings.nonce);
            fetch(settings.url, {
                method: 'POST',
                body: formData,
            }).then(function (request){
                request.json().then(function (response){
                    if (response.success){
                        form.reset();
                        addNotification(form, response.data);
                    } else {
                        addNotification(form, response.data, true);
                    }
                    btn.removeAttribute('disabled');
                })
            }).catch(function (error){
                btn.removeAttribute('disabled');
                addNotification(form, error.msg, true);
            })
        } else {
            addNotification(form, settings.msg, true);
        }
    });
}, false);

function addNotification(form, text, isError){
    const ntf = document.createElement('p');
    ntf.appendChild(document.createTextNode(text));
    ntf.classList.add(isError ? 'el-post-form-error' : 'el-post-form-info');
    form.appendChild(ntf);
    setTimeout(function (){
        ntf.remove();
    }, 5000);
}
