import { useCallback, useState } from "react";
import { router } from "@inertiajs/react";
import { Task, TaskStatus } from "@/types/task";

interface Options {
    onError?: (taskId: string, message: string) => void;
}

export function useTaskStatusChange(
    tasks: Task[],
    setTasks: (tasks: Task[]) => void,
    { onError }: Options = {},
) {
    const [pendingTaskId, setPendingTaskId] = useState<string | null>(null);

    const changeStatus = useCallback(
        (taskId: string, status: TaskStatus) => {
            const task = tasks.find((task) => task.id === taskId);

            if (!task) {
                return;
            }

            const previousTasks = tasks;

            const updatedTasks = tasks.map((task) =>
                task.id === taskId ? { ...task, status } : task,
            );

            setTasks(updatedTasks);
            setPendingTaskId(taskId);

            router.patch(
                `/tasks/${taskId}/status`,
                { status },
                {
                    preserveScroll: true,

                    onError: (errors) => {
                        setTasks(previousTasks);

                        onError?.(
                            taskId,
                            errors.status ?? "Nie udało się zmienić statusu.",
                        );
                    },

                    onFinish: () => {
                        setPendingTaskId(null);
                    },
                },
            );
        },
        [tasks, setTasks, onError],
    );

    const isPending = (taskId: string) => {
        return pendingTaskId === taskId;
    };

    return {
        changeStatus,
        isPending,
    };
}
