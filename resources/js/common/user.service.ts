import User from './model/user.model';

export default class UserService {
    private _user: User

    get user(): User {
        return this._user
    }

    async getTestUsers(): Promise<User[]> {
        return [];
    }
}
