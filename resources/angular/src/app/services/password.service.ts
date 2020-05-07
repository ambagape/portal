import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

@Injectable({
    providedIn: 'root'
})
export class PasswordService {

    constructor(
        private http: HttpClient
    ) {
    }

    private static setOptions(): { headers: HttpHeaders } {
        return {
            headers: new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded')
        };
    }

    public requestPassword(email: string): Observable<null> {
        let body = new HttpParams();
        body = body.set('PARAM', email);

        return this.http.post<null>(environment.baseUrl + '/ZendNieuwWachtwoordMail', body.toString(), {...PasswordService.setOptions()});
    }

    public setPassword(password: string, token: string): Observable<{ gebruikerstype: number }> {
        let body = new HttpParams();
        body = body.set('Token', token);
        body = body.set('Wachtwoord', password);

        return this.http
            .post<{ gebruikerstype: number }>(
                environment.baseUrl + '/ZetNieuwWachtwoord',
                body.toString(),
                {...PasswordService.setOptions()}
                );
    }

}
