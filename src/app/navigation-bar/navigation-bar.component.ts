import { Component, OnInit } from '@angular/core';
import { RecipeService } from '../recipe.service';
import { UserService } from '../user.service';
import { User } from "../recipe";
import * as $AB from 'jquery';
import * as bootstrap from "bootstrap";


@Component({
    selector: 'app-navigation-bar',
    templateUrl: './navigation-bar.component.html',
    styleUrls: ['./navigation-bar.component.css']
})

export class NavigationBarComponent implements OnInit {

    // acho que eh uma variavel que deveria ser meio "global" ao sistema, mas por enquanto vou deixar ela definida aqui
    // para testar a parte do login
    isLoggedIn: boolean;
    searchTerm: string;
    username: string;
    fullname: string;
    email: string;
    password: string;
    passwordHashed: string;

    // Config variables
    usernameConfig: string;
    fullnameConfig: string;
    emailConfig: string;
    oldPasswordConfig: string;
    passwordConfig: string;
    passwordConfirmationConfig: string;

    //Session variables
    usernameSession: string;
    fullnameSession: string;
    emailSession: string;

    user_list: User[];
    new_user: User;


    constructor(private recipeService: RecipeService, private userService : UserService) {
        console.log("constructor nav bar");

        this.userService.getCurretUser().subscribe(
            user => {
                console.log("GOT cookie");
                this.isLoggedIn = true;
                this.fullnameSession = user.fullname;
                this.emailSession = user.email;
                this.usernameSession = user.username;
                this.recipeService.login(user);
             }, 
            error => {
                console.log("no cookie I guess");
            }
        );


        // this.isLoggedIn = this.userService.isLoggedIn;
        // if(this.isLoggedIn) {
        //     console.log("IN");
        //     this.fullnameSession = this.userService.currentUser.fullname;
        //     this.emailSession = this.userService.currentUser.email;
        //     this.usernameSession = this.userService.currentUser.username;
        //     this.recipeService.login(this.userService.currentUser);
        // }
    }

    ngOnInit() {
    }

    checkField(field, elementId): number {
        if (!field) {
            var element = document.getElementById(elementId);
            element.classList.add("is-invalid");
            return 0;
        }
        else {
            var element = document.getElementById(elementId);
            element.classList.remove("is-invalid");
            return 1;
        }
    }
    submitLogin(): void {
        console.log("submit login");

        // usar this.username e this.password para autenticação
        if (!this.checkField(this.username, "loginUsername") || !this.checkField(this.password, "loginPassword"))
            return;

        this.userService.login(this.username, this.password).subscribe(
            user => {
                // All went fine, user was logged.
                $('#loginModal').modal('hide');
                $('#loginModal .close').click();
                var element = document.getElementById("loginUsername");
                element.classList.remove("is-invalid");
                var element = document.getElementById("loginPassword");
                element.classList.remove("is-invalid");
                console.log("User " + this.username + " authenticated");
                console.log(user);
                this.isLoggedIn = true;
                this.fullnameSession = user.fullname;
                this.emailSession = user.email;
                this.usernameSession = user.username;
                this.recipeService.login(user);

                // Print cookies
                // var theCookies = document.cookie.split(';');
                // var aString = '';
                // for (var i = 0 ; i <= theCookies.length; i++) {
                //     aString  = theCookies[i] + "\n";
                //     console.log(aString);
                // }

            },
            err => {
                // Something went wrong, handle what.
                if(err.status == 404) {
                    // User does not exist.
                    var element = document.getElementById("loginUsername");
                    element.classList.add("is-invalid");
                    console.log("User not found");
                } else if(err.status == 401) {
                    // Wrong password.
                    var element = document.getElementById("loginPassword");
                    element.classList.add("is-invalid");
                    console.log("Password does not match");
                } else {
                    console.log(err.status);
                }
            }
        )
        return;
    }

    submitNewUser(): void {
        console.log("submit new user");

        if (!this.checkField(this.usernameConfig, "usernameConfig")
            || !this.checkField(this.fullnameConfig, "fullnameConfig")
            || !this.checkField(this.emailConfig, "emailConfig")
            || !this.checkField(this.passwordConfig, "passwordConfig")
        ) {
            return;
        }
        
        // If the passwords do not match.
        if (this.passwordConfig != this.passwordConfirmationConfig) {
            var element = document.getElementById("passwordConfirmationConfig");
            element.classList.add("is-invalid");
            this.passwordConfig = null;
            this.passwordConfirmationConfig = null;
            return;
        }
        // Check basic mail.
        var email = this.emailConfig.split("@");
        if (email.length < 2) {
            var element = document.getElementById("emailConfig");
            element.classList.add("is-invalid");
            return;
        } else {
            var emailDomain = email[1].split(".");
            if (emailDomain.length < 2) {
                var element = document.getElementById("emailConfig");
                element.classList.add("is-invalid");
                return;
            }
        }
        
        // Build the user object.
        this.new_user = new User();
        this.new_user.username = this.usernameConfig;
        this.new_user.password = this.passwordConfig;
        this.new_user.fullname = this.fullnameConfig;
        this.new_user.email = this.emailConfig;

        // Clear this basic info.
        this.passwordConfig = null;
        this.passwordConfirmationConfig = null;
        
        // Send the request, handle possible errors.
        this.userService.newUser(this.new_user).subscribe(
            user_id => {
                        this.new_user.id = user_id;
                        if (this.new_user.id != -1) {
                            console.log("User " + this.new_user.id + " created succesfully");
                            $('#loginConfigurationModal').modal('hide');
                            $('#loginConfigurationModal .close').click();
                        } else {
                            console.log("User creation failed");
                        }
        }, err => {
            // Something went wrong, handle what.
            if(err.status == 409) {
                // User already exists.
                var element = document.getElementById("usernameConfig");
                element.classList.add("is-invalid");
            } else if(err.status == 500) {
                // Something went wrong in DB.
            } else {
                console.log(err.status);
            }
        });
        return;
    }
    
    submitChangesInUser(): void {
        // if (
        //     !this.checkField(this.fullnameConfig, "fullnameConfig")
        //     || !this.checkField(this.emailConfig, "emailConfig")
        //     || !this.checkField(this.passwordConfig, "passwordConfig")
        // ) {
        //     return;
        // }
        // this.passwordHashed = this.oldPasswordConfig.toString();
        // this.oldPasswordConfig = null;
        // this.recipeService.searchUsers(this.username)
        //     .subscribe(user_list => {
        //         this.user_list = user_list;
        //         if (this.user_list.length > 0) {
        //             if (this.passwordHashed != this.user_list[0].password) {
        //                 var element = document.getElementById("oldPasswordConfig");
        //                 element.classList.add("is-invalid");
        //                 console.log("Password does not match");
        //                 return;
        //             }
        //             console.log("Editing user" + this.user_list[0].id);
        //             if (this.passwordConfig != this.passwordConfirmationConfig) {
        //                 var element = document.getElementById("passwordConfirmationConfig");
        //                 element.classList.add("is-invalid");
        //                 this.passwordConfig = null;
        //                 this.passwordConfirmationConfig = null;
        //                 console.log("Password confirmation does not match");
        //                 return;
        //             }
        //             this.passwordConfig = null;
        //             this.passwordConfirmationConfig = null;
        //             this.new_user = new User();
        //             this.new_user.username = this.username;
        //             this.new_user.password = this.passwordHashed;
        //             this.new_user.fullname = this.fullnameConfig;
        //             this.new_user.email = this.emailConfig;
        //             $('#loginConfigurationModal').modal('hide');
        //             $('#loginConfigurationModal .close').click();

        //             this.userService.editUser(this.new_user)
        //                 .subscribe(user_id => {
        //                     this.new_user.id = user_id;
        //                     if (this.new_user.id != -1) {
        //                         console.log("User " + this.new_user.id + " edited succesfully");
        //                         this.fullnameSession = this.new_user.fullname;
        //                         this.emailSession = this.new_user.email;
        //                         this.usernameSession = this.new_user.username;
        //                     } else {
        //                         console.log("User edit failed");
        //                     }
        //                 });
        //             return;
        //         } else {
        //             console.log("User not found");
        //             return;
        //         }

        //     });
    }

    getUserLevelName()
    {
        var level = this.recipeService.getUserLevel();
        if(level == 2)
            return "Administrador";
        if(level == 1)
            return "Usuário com Permissões";
        return "Usuário sem Permissões";
    }


    logout(): void {
        console.log("logout");
        this.userService.logout();
    }

    deleteUserAccount(): void {
        console.log("DELETE");
    }

}
