export interface KeyValue {
    key: string;
    value: string;
}

export interface CodeFrequency {
    code: string;
    frequency: string;
}

export interface ValidityCheck {
    valid: boolean,
    error?: string
}

export interface ListResults {
    data: any[],
    total_results: number
}

export interface UpdateResult {
    success: boolean,
    error?: string,
}
