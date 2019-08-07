import { Component, OnInit } from '@angular/core';
import { Chart } from 'chart.js';
import { Tag } from "../recipe";
import { Recipe } from '../recipe';
import { RecipeService } from '../recipe.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {

  tags: Tag[];
  recipes: Recipe[];
  numberOfRecipes: number;
  numberOfUsers: number;
  userLevel: number;
  isLoggedIn: boolean;

  constructor(private recipeService: RecipeService, private router: Router) { }

  ngOnInit() {

    this.recipeService.getTags()
      .subscribe(tags => this.tags = tags);

    this.recipeService.getRecipes()
      .subscribe(recipes => this.recipes = recipes);

    this.recipeService.getNumberOfRecipes().subscribe(num => this.numberOfRecipes = num);
    this.recipeService.getNumberOfUsers().subscribe(num => this.numberOfUsers = num);

    this.isLoggedIn = this.recipeService.isLoggedIn;
    this.userLevel = this.recipeService.getUserLevel();

    /*
    var graphData;
    this.recipeService.organizeChartData().subscribe(data => graphData = data);
    this.createGraph(graphData);*/

   // $('.next').click(function () { $('.carousel').carousel('next'); return false; });
   // $('.prev').click(function () { $('.carousel').carousel('prev'); return false; });

  }

  next()
  {
    $('.carousel').carousel('next'); return false;
  }

  prev()
  {
    $('.carousel').carousel('prev'); return false;
  }

  goToNewRecipePage() {
    if (this.recipeService.isUserAllowedToCreateRecipe()) {
      this.router.navigateByUrl('/details/new');
    }
    else {
      window.alert("Você não possui autorização para adicionar receitas!");
    }
  }
  recipeCarouselCounter(i: number) {
    return new Array(Math.ceil(i/3));
}

  getImageSrc(index, recipe) {
    return "../../../../backend/uploads/" + recipe.photos[index];
  }

  createGraph(data): void {
    var ctx = document.getElementById("myChart");
    var myChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: data.labels,
        datasets: [{
          data: data.data,
          backgroundColor: data.colors
        }]
      },
      options: {
        legend: {
          display: false
        },
        title: {
          display: true,
          text: 'Número de Receitas por Categoria',
          fontSize: 30
        },
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true,
              fontSize: 20
            }
          }],
          xAxes: [{
            ticks: {
              beginAtZero: true,
              fontSize: 20
            }
          }]
        }
      }
    });
  }

}
