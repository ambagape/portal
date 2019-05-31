import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-password-reset',
  templateUrl: './password-reset.component.html',
  styleUrls: ['./password-reset.component.scss']
})
export class PasswordResetComponent implements OnInit {

  public showSuccess = false;

  constructor() { }

  ngOnInit() {
  }

  reset() {
    this.showSuccess = !this.showSuccess;
  }

}
