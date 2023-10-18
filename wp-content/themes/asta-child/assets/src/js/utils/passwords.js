/**
 * The function checks if two password inputs match.
 * @param pwd - The password entered by the user.
 * @param repeat_pwd - The parameter "repeat_pwd" is a string representing the repeated password
 * entered by the user.
 * @returns The function `check_pwd_validity` returns a boolean value (`true` or `false`) based on
 * whether the `pwd` and `repeat_pwd` parameters are equal and not empty. If they are equal and not
 * empty, the function returns `true`, otherwise it returns `false`.
 */
export const check_pwd_validity = (pwd, repeat_pwd) => {

    if (pwd === repeat_pwd && '' !== repeat_pwd) {
        return true;
    }

    return false;
}