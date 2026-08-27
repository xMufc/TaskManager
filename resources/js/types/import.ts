export interface ImportRowAccepted {
    row: number;
    title: string;
}

export interface ImportRowRejected {
    row: number;
    reason: string;
}

export interface ImportResult {
    id: string;
    status: "processing" | "completed";
    accepted: ImportRowAccepted[];
    rejected: ImportRowRejected[];
}
