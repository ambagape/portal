import { AbstractControl, ValidationErrors, ValidatorFn } from '@angular/forms';

export const mustNotContainValidator = (
    blacklist: string ,
    error: ValidationErrors
): ValidatorFn => {
    return (control: AbstractControl): { [key: string]: any } => {
        if (!control.value) {
            // if control is empty return no error
            return null;
        }

        const valid = control.value.indexOf(blacklist) === -1;

        // if true, return no error (no error), else return error passed in the second parameter
        return valid ? null : error;
    };
};
