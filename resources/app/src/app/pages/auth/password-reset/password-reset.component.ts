import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

import { ActivatedRoute } from '@angular/router';
import { PasswordService } from '../../../services/password.service';
import { mustNotContainValidator } from 'src/app/validators/must-not-contain.validator';
import { passwordMatchValidator } from 'src/app/validators/password-match.validator';
import { patternValidator } from 'src/app/validators/pattern.validator';

@Component({
    selector: 'app-password-reset',
    templateUrl: './password-reset.component.html',
    styleUrls: ['./password-reset.component.scss']
})
export class PasswordResetComponent implements OnInit {

    public showSuccess = false;
    public showError = false;
    public passwordDontMatch = false;

    public token: string;

    public form: FormGroup;

    constructor(private activatedRoute: ActivatedRoute, private formBuilder: FormBuilder, private passwordService: PasswordService) {
        this.activatedRoute.params.subscribe((data: { token: string }) => {
            this.token = data.token;
        });
    }

    ngOnInit() {
        this.showSuccess = false;
        this.showError = false;
        this.passwordDontMatch = false;

        this.form = this.formBuilder.group({
            password: [
                undefined,
                [
                    // 1. Password Field is Required
                    Validators.required,
                    // 2. check whether the entered password has a number
                    patternValidator(/\d/, { hasNumber: true }),
                    // 3. check whether the entered password has upper case letter
                    patternValidator(/[A-Z]/, { hasCapitalCase: true }),
                    // 4. check whether the entered password has a lower-case letter
                    patternValidator(/[a-z]/, { hasSmallCase: true }),
                    // 5. must contain one or more of below
                    patternValidator(/[@%$#!^&*()]/, { hasSpecialCharacters: true }),
                    // 6. check whether the entered password has a forbidden special character
                    mustNotContainValidator('+', { hasWrongSpecialCharacters: true }),
                    // 7. Has a minimum length of 8 characters
                    Validators.minLength(8)

                ]
            ],
            confirm: [
                undefined,
                [
                    // 1. Password Field is Required
                    Validators.required,
                    // 2. check whether the entered password has a number
                    patternValidator(/\d/, { hasNumber: true }),
                    // 3. check whether the entered password has upper case letter
                    patternValidator(/[A-Z]/, { hasCapitalCase: true }),
                    // 4. check whether the entered password has a lower-case letter
                    patternValidator(/[a-z]/, { hasSmallCase: true }),
                    // 5. must contain one or more of below
                    patternValidator(/[@%$#!^&*()]/, { hasSpecialCharacters: true }),
                    // 6. check whether the entered password has a forbidden special character
                    mustNotContainValidator('+', { hasWrongSpecialCharacters: true }),
                    // 7. Has a minimum length of 8 characters
                    Validators.minLength(8)
                ]
            ]
        }, {
            validator: passwordMatchValidator
        });
    }

    reset() {
        this.passwordDontMatch = false;

        // console.log(this.form.get('password').errors);

        if (!this.form.valid) {
            this.form.markAllAsTouched();
            return;
        }

        if (this.form.get('password').value !== this.form.get('confirm').value) {
            this.passwordDontMatch = true;
            return;
        }

        this.passwordService.setPassword(this.form.get('password').value, this.token).subscribe(() => {
            this.showSuccess = true;
            this.showError = false;
        }, () => {
            this.showSuccess = false;
            this.showError = true;
        });
    }

}
