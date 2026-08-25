export type TaskStatus =
    | "todo"
    | "in_progress"
    | "done"
    | "blocked"
    | "cancelled";
export type TaskPriority = "low" | "medium" | "high" | "urgent";

export interface Task {
    id: string;
    title: string;
    description: string | null;
    status: TaskStatus;
    priority: TaskPriority;
    dueDate: string | null;
    allowedNextStatuses: TaskStatus[];
}

export const STATUSES: TaskStatus[] = [
    "todo",
    "in_progress",
    "done",
    "blocked",
    "cancelled",
];

export const STATUS_LABELS: Record<TaskStatus, string> = {
    todo: "Do zrobienia",
    in_progress: "W trakcie",
    done: "Zakończone",
    blocked: "Zablokowane",
    cancelled: "Anulowane",
};

export const PRIORITY_LABELS: Record<TaskPriority, string> = {
    low: "Niski",
    medium: "Średni",
    high: "Wysoki",
    urgent: "Pilne",
};

export const STATUS_STYLES: Record<TaskStatus, string> = {
    todo: "bg-slate-300 text-slate-700",
    in_progress: "bg-blue-100 text-blue-700",
    done: "bg-emerald-100 text-emerald-700",
    blocked: "bg-orange-100 text-orange-800",
    cancelled: "bg-rose-100 text-rose-700",
};

export const PRIORITY_STYLES: Record<TaskPriority, string> = {
    low: "bg-slate-300 text-slate-700",
    medium: "bg-blue-100 text-blue-700",
    high: "bg-amber-100 text-amber-800",
    urgent: "bg-rose-100 text-rose-700",
};
