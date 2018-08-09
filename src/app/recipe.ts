export class Recipe {
    id: number;
    name: string;
    duration: string;
    difficulty: number; // 1 to 5
    servings: number;
    description: string;
    ingredients: Array<Ingredient>;
    preparation: Array<string>;
    tags: Array<number>;
}

export class Ingredient {
    id: number;
    name: string;
    amount: number;
    unit: string;
}

export class Tag {
    id: number;
    name: string;
    icon: string;
    color: string;
}

export class ChartFormat {
    labels: string[];
    data: number[];
    colors: string[];
}