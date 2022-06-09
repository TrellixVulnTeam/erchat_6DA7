'use strict'

// input fields focus effects:
const textInputs = document.querySelectorAll('input');

textInputs.forEach(textInput => {
    textInput.addEventListener('focus', () => {
        let parent = textInput.parentNode;
        parent.classList.add('active');
    });

    textInput.addEventListener('blur', () => {
        let parent = textInput.parentNode;
        parent.classList.remove('active');
    });
});

// password show/hide:
const passwordInput = document.querySelector('.password-input');
const eyeBtn = document.querySelector('.eye-btn');

eyeBtn.addEventListener('click', () => {
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeBtn.innerHTML = "<span class='material-icons-sharp'> visibility </span>";
    } else {
        passwordInput.type = 'password';
        eyeBtn.innerHTML = "<span class='material-icons-sharp'> visibility_off </span>";
    }
});


// change login/register form:
const signInForm = document.querySelector('.sign-in-form');
const signUpForm = document.querySelector('.sign-up-form');
const toSignUp = document.querySelector('.link-to-sign-up');
const toSignIn = document.querySelector('.link-to-sign-in');

toSignUp.addEventListener('click', () => {
    signInForm.classList.add('hidden');
    signUpForm.classList.remove('hidden');
});

toSignIn.addEventListener('click', () => {
    signInForm.classList.remove('hidden');
    signUpForm.classList.add('hidden');
});



