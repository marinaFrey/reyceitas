export class Recipe {
    id: number;
    name: string;
    duration: string;
    difficulty: number; // 1 to 5
    servings: number;
    description: string;
    ingredients: Array<Ingredient>;
    preparation: Array<string>;
}

export class Ingredient {
    id: number;
    name: string;
    amount: number;
    unit: string;
}