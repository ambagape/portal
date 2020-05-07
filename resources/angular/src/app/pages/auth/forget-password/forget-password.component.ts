import { Component, OnInit } from '@angular/core';
import { PasswordService } from '../../../services/password.service';

@Component({
    selector: 'app-forget-password',
    templateUrl: './forget-password.component.html',
    styleUrls: ['./forget-password.component.scss']
})
export class ForgetPasswordComponent implements OnInit {

    public showSuccess = false;
    public showError = false;
    public email = '';

    constructor(private passwordService: PasswordService) {
    }

    ngOnInit() {
        this.showSuccess = false;
        this.showError = false;
    }

    reset() {
        if (!this.email || this.email.length < 0) {
            return;
        }

        this.passwordService.requestPassword(this.email).subscribe(() => {
            this.showSuccess = true;
            this.showError = false;
        }, () => {
            this.showSuccess = false;
            this.showError = true;
        });
    }

}
