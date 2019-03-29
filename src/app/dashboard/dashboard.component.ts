import { Component, OnInit } from '@angular/core';
import { Chart } from 'chart.js';
import { Tag } from "../recipe";
import { RecipeService } from '../recipe.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {

  tags: Tag[];
  numberOfRecipes: number;
  numberOfUsers: number;

  constructor(private recipeService: RecipeService, private router: Router) { }

  ngOnInit() {

    this.recipeService.getTags()
      .subscribe(tags => this.tags = tags);

    this.recipeService.getNumberOfRecipes().subscribe(num => this.numberOfRecipes = num);
    this.recipeService.getNumberOfUsers().subscribe(num => this.numberOfUsers = num);

    var graphData;
    this.recipeService.organizeChartData().subscribe(data => graphData = data);
    this.createGraph(graphData);
  }

  goToNewRecipePage() {
    if (this.recipeService.getUserLevel() > 0) {
      this.router.navigateByUrl('/details/new');
    }
    else {
      window.alert("Você não possui autorização para realizar modificações no banco de dados!");
    }
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
