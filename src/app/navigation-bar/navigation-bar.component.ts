import { Component, OnInit } from '@angular/core';

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
  password: string;
  
  constructor() { }

  ngOnInit() {
  }

  // chamar aqui funções de autenticação de login
  submitLogin(): void
  {
    // usar this.username e this.password para autenticação
    console.log(this.username,this.password);
    this.isLoggedIn = true;
  }



}
