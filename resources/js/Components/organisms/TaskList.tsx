import { useEffect, useState } from "react";
import { Task } from "@/types/task";
import { useTaskStatusChange } from "@/Hooks/useTaskStatusChange";
import TaskRow from "./TaskRow";

interface Props {
    tasks: Task[];
    onEdit: (task: Task) => void;
    onDelete: (task: Task) => void;
}

export default function TaskList({
    tasks: initialTasks,
    onEdit,
    onDelete,
}: Props) {
    const [tasks, setTasks] = useState(initialTasks);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        setTasks(initialTasks);
    }, [initialTasks]);

    const { changeStatus, isPending } = useTaskStatusChange(tasks, setTasks, {
        onError: (_, message) => setError(message),
    });

    if (tasks.length === 0) {
        return (
            <p className="py-8 text-center text-slate-500">
                Brak zadań do wyświetlenia.
            </p>
        );
    }

    return (
        <div>
            {error && (
                <div className="mb-3 rounded-md bg-rose-50 px-3 py-2 text-sm text-rose-700">
                    {error}
                </div>
            )}

            <table className="w-full table-fixed border-collapse">
                <thead>
                    <tr className="border-b text-left text-xs uppercase text-slate-500">
                        <th className="w-[23rem] whitespace-nowrap px-4 py-2">
                            Zadanie
                        </th>

                        <th className="w-[8rem] whitespace-nowrap px-4 py-2">
                            Status
                        </th>

                        <th className="w-[8rem] whitespace-nowrap px-4 py-2">
                            Priorytet
                        </th>

                        <th className="w-[8rem] whitespace-nowrap px-4 py-2">
                            Termin
                        </th>

                        <th className="w-[11rem] whitespace-nowrap px-4 py-2">
                            Zmień status
                        </th>

                        <th className="w-[8rem] whitespace-nowrap px-4 py-2 text-right">
                            Akcje
                        </th>
                    </tr>
                </thead>

                <tbody className="divide-y">
                    {tasks.map((task) => (
                        <TaskRow
                            key={task.id}
                            task={task}
                            isPending={isPending(task.id)}
                            onStatusChange={(status) =>
                                changeStatus(task.id, status)
                            }
                            onEdit={() => onEdit(task)}
                            onDelete={() => onDelete(task)}
                        />
                    ))}
                </tbody>
            </table>
        </div>
    );
}
