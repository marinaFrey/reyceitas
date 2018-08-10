import { Component, OnInit } from '@angular/core';
import { Chart } from 'chart.js';
import { Tag } from "../recipe";
import { RecipeService }  from '../recipe.service';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {

  tags: Tag[];
  numberOfRecipes: number;

  constructor(private recipeService: RecipeService) { }

  ngOnInit() {

    this.recipeService.getTags()
        .subscribe(tags => this.tags = tags);

    this.recipeService.getNumberOfRecipes().subscribe(num => this.numberOfRecipes = num);
    
    var graphData;
    this.recipeService.organizeChartData().subscribe(data => graphData = data);
    this.createGraph(graphData);  
  }

  createGraph(data): void
  {
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
