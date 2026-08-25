import { useEffect } from "react";
import { useForm } from "@inertiajs/react";
import { Task, TaskPriority, PRIORITY_LABELS } from "@/types/task";

import Input from "@/Components/atoms/Input";
import Textarea from "@/Components/atoms/Textarea";
import Select from "@/Components/atoms/Select";

interface Props {
    task: Task | null;
    open: boolean;
    onClose: () => void;
}

export default function TaskFormModal({ task, open, onClose }: Props) {
    const form = useForm({
        title: "",
        description: "",
        priority: "medium" as TaskPriority,
        dueDate: "",
    });

    useEffect(() => {
        if (!task) {
            form.reset();
            return;
        }

        form.setData({
            title: task.title,
            description: task.description ?? "",
            priority: task.priority,
            dueDate: task.dueDate ?? "",
        });
    }, [task]);

    if (!open) {
        return null;
    }

    const handleSubmit = (event: React.FormEvent) => {
        event.preventDefault();

        const options = {
            onSuccess: onClose,
        };

        if (task) {
            form.put(`/tasks/${task.id}`, options);
            return;
        }

        form.post("/tasks", options);
    };

    const isEditing = Boolean(task);

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <form
                onSubmit={handleSubmit}
                className="w-full max-w-md space-y-4 rounded-lg bg-white p-6 shadow-xl"
            >
                <h2 className="text-lg font-semibold text-slate-900">
                    {isEditing ? "Edytuj zadanie" : "Nowe zadanie"}
                </h2>

                <div>
                    <Input
                        value={form.data.title}
                        onChange={(event) =>
                            form.setData("title", event.target.value)
                        }
                        placeholder="Tytuł"
                    />

                    {form.errors.title && (
                        <p className="mt-1 text-sm text-rose-600">
                            {form.errors.title}
                        </p>
                    )}
                </div>

                <Textarea
                    value={form.data.description}
                    onChange={(event) =>
                        form.setData("description", event.target.value)
                    }
                    placeholder="Opis (opcjonalnie)"
                    rows={4}
                />

                <Select
                    value={form.data.priority}
                    onChange={(event) =>
                        form.setData(
                            "priority",
                            event.target.value as TaskPriority,
                        )
                    }
                >
                    {Object.entries(PRIORITY_LABELS).map(([value, label]) => (
                        <option key={value} value={value}>
                            {label}
                        </option>
                    ))}
                </Select>

                <Input
                    type="date"
                    value={form.data.dueDate}
                    onChange={(event) =>
                        form.setData("dueDate", event.target.value)
                    }
                />

                <div className="flex justify-end gap-2 pt-2">
                    <button
                        type="button"
                        onClick={onClose}
                        className="px-4 py-2 text-sm text-slate-600 hover:text-slate-900"
                    >
                        Anuluj
                    </button>

                    <button
                        type="submit"
                        disabled={form.processing}
                        className="
                            rounded-md
                            bg-slate-800
                            px-4 py-2
                            text-sm
                            font-medium
                            text-white
                            hover:bg-slate-700
                            disabled:cursor-not-allowed
                            disabled:opacity-50
                        "
                    >
                        {isEditing ? "Zapisz" : "Utwórz"}
                    </button>
                </div>
            </form>
        </div>
    );
}
