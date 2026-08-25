import { STATUSES, TaskStatus, STATUS_LABELS } from "@/types/task";

interface Props {
    current: TaskStatus | null;
    onChange: (status: TaskStatus | null) => void;
}

export default function TaskFilterBar({ current, onChange }: Props) {
    return (
        <div className="flex flex-wrap gap-2">
            <button
                onClick={() => onChange(null)}
                className={`rounded-full px-3 py-1 text-sm ${
                    current === null
                        ? "bg-slate-800 text-white"
                        : "bg-slate-100 text-slate-600"
                }`}
            >
                Wszystkie
            </button>
            {STATUSES.map((status) => (
                <button
                    key={status}
                    onClick={() => onChange(status)}
                    className={`rounded-full px-3 py-1 text-sm ${
                        current === status
                            ? "bg-slate-800 text-white"
                            : "bg-slate-100 text-slate-600"
                    }`}
                >
                    {STATUS_LABELS[status]}
                </button>
            ))}
        </div>
    );
}
