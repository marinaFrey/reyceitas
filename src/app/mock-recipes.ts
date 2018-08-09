import { Recipe } from './recipe';
import { Tag } from './recipe';

export const RECIPES: Recipe[] = 
[
  { id: 13, name: 'Peixe', duration: "1:00H", difficulty: 4, servings: 3, description: 'que nem a do outback', ingredients: [{id:1,name:"sal",amount:2,unit:"pitadas"},{id:2,name:"pimenta",amount:4,unit:"pitadas"}], preparation: ["step1", "step2", "step3", "step4"], tags:[1,2,3]},
  { id: 14, name: 'Pudim', duration: "1:00H", difficulty: 4, servings: 3, description: 'que nem a do outback', ingredients: [{id:1,name:"sal",amount:2,unit:"pitadas"},{id:2,name:"pimenta",amount:4,unit:"pitadas"}], preparation: ["step1", "step2", "step3", "step4"], tags:[1,11,4]},
  { id: 15, name: 'Unicornio Frito', duration: "1:00H", difficulty: 4, servings: 3, description: 'que nem a do outback', ingredients: [{id:1,name:"sal",amount:2,unit:"pitadas"},{id:2,name:"pimenta",amount:4,unit:"pitadas"}], preparation: ["step1", "step2", "step3", "step4"], tags:[1,10,3]},
  { id: 16, name: 'Panquecas', duration: "1:00H", difficulty: 4, servings: 3, description: 'que nem a do outback', ingredients: [{id:1,name:"sal",amount:2,unit:"pitadas"},{id:2,name:"pimenta",amount:4,unit:"pitadas"}], preparation: ["step1", "step2", "step3", "step4"], tags:[1,4,3]},
  { id: 17, name: 'Sushi', duration: "1:00H", difficulty: 4, servings: 3, description: 'que nem a do outback', ingredients: [{id:1,name:"sal",amount:2,unit:"pitadas"},{id:2,name:"pimenta",amount:4,unit:"pitadas"}], preparation: ["step1", "step2", "step3", "step4"], tags:[1,2,5]},
  { id: 18, name: 'Caviar', duration: "1:00H", difficulty: 4, servings: 3, description: 'que nem a do outback', ingredients: [{id:1,name:"sal",amount:2,unit:"pitadas"},{id:2,name:"pimenta",amount:4,unit:"pitadas"}], preparation: ["step1", "step2", "step3", "step4"], tags:[1,2,0]},
  { id: 19, name: 'Omuraisu', duration: "1:00H", difficulty: 4, servings: 3, description: 'que nem a do outback', ingredients: [{id:1,name:"sal",amount:2,unit:"pitadas"},{id:2,name:"pimenta",amount:4,unit:"pitadas"}], preparation: ["step1", "step2", "step3", "step4"], tags:[1,2,8]},
  { id: 20, name: 'Ovos Cozidos', duration: "1:00H", difficulty: 4, servings: 3, description: 'que nem a do outback', ingredients: [{id:1,name:"sal",amount:2,unit:"pitadas"},{id:2,name:"pimenta",amount:4,unit:"pitadas"}], preparation: ["step1", "step2", "step3", "step4"], tags:[1,7,3]}
];

export const TAGS: Tag[] =
[
  {id:0, name:"favoritos", icon: "fa-star", color: "#dfc013"},
  {id:1, name:"bebidas", icon: "fa-coffee", color: "#915721"},
  {id:2, name:"sobremesas", icon: "fa-birthday-cake", color: "#dd73d8"},
  {id:3, name:"vegetariano", icon: "fa-feather-alt", color: "#72ce6f"},
  {id:4, name:"refeições", icon: "fa-utensils", color: "#777777"},
  {id:5, name:"sopas", icon: "fa-utensil-spoon", color: "#6fcebe"},
  {id:6, name:"lanches", icon: "fa-cookie-bite", color: "#926d4b"},
  {id:7, name:"peixes", icon: "fa-fish", color: "#6f98ce"},
  {id:8, name:"aves", icon: "fa-crow", color: "#ce926f"},
  {id:9, name:"porco", icon: "fa-piggy-bank", color: "#ce926f"},
  {id:10, name:"carne vermelha", icon: "fa-chess-knight", color: "#ce926f"},
  {id:11, name:"saudável", icon: "fa-apple-alt", color: "#9dce6f"}
]