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
  {id:0, name:"favoritos"},
  {id:1, name:"bebidas"},
  {id:2, name:"sobremesas"},
  {id:3, name:"vegetariano"},
  {id:4, name:"refeições"},
  {id:5, name:"sopas"},
  {id:6, name:"lanches"},
  {id:7, name:"peixes"},
  {id:8, name:"aves"},
  {id:9, name:"porco"},
  {id:10, name:"carne vermelha"},
  {id:11, name:"saudável"}
]