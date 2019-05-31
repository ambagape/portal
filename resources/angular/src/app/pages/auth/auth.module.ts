import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AuthRoutingModule } from './auth-routing.module';
import { PasswordResetComponent } from './password-reset/password-reset.component';
import { ForgetPasswordComponent } from './forget-password/forget-password.component';

@NgModule({
    declarations: [
        PasswordResetComponent,
        ForgetPasswordComponent
    ],
    imports: [
        CommonModule,
        AuthRoutingModule
    ]
})
export class AuthModule {
}
