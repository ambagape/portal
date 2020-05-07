import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { PasswordService } from '../../../services/password.service';

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
            password: [undefined, [Validators.required, Validators.minLength(8)]],
            confirm: [undefined, [Validators.required, Validators.minLength(8)]]
        });
    }

    reset() {
        this.passwordDontMatch = false;

        if (!this.form.valid) {
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
