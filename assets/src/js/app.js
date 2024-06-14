const postForm = null;
const singletonEnforcer = Symbol();

class PostForm {

    constructor(enforcer) {
        if (enforcer !== singletonEnforcer) {
            throw new Error('Cannot construct new PostForm. Please use static instance');
        }

        this.init();
    }

    static get instance() {
        if (!this[postForm]) {
            this[postForm] = new PostForm(singletonEnforcer);
        }
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.form = document.querySelector("#post_form");
            this.form.addEventListener("submit", this.formSubmit, false);
        }, false);
    }

    formSubmit = (e) => {
        e.preventDefault();
        window.tinyMCE.triggerSave();

        this.btn = document.querySelector("#post_form button");
        this.btn.setAttribute('disabled', true);
        const formData = new FormData(e.target);
        const formProps = Object.fromEntries(formData);

        if (formProps.post_title && formProps.post_content) {
            formData.append('action', 'propose_post_form');
            formData.append('nonce', settings.nonce);
            fetch(settings.url, {
                method: 'POST',
                body: formData,
            })
                .then(this.submitResolve)
                .catch(this.submitReject)
        } else {
            this.addNotification(this.form, settings.msg, true);
        }
    }

    submitResolve = (request) => {
        request.json().then((response) =>{
            if (response.success){
                form.reset();
                this.addNotification(this.form, response.data);
            } else {
                this.addNotification(this.form, response.data, true);
            }
            btn.removeAttribute('disabled');
        });
    }

    submitReject = (error) => {
        this.btn.removeAttribute('disabled');
        this.addNotification(this.form, error.msg, true);
    }

    addNotification = (form, text, isError) => {
        const ntf = document.createElement('p');
        ntf.appendChild(document.createTextNode(text));
        ntf.classList.add(isError ? 'el-post-form-error' : 'el-post-form-info');
        form.appendChild(ntf);
        setTimeout(() => {
            ntf.remove();
        }, 5000);
    }
}

PostForm.instance;
