import { useState } from "react";
import { Head, router } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Task, TaskStatus } from "@/types/task";
import { useTaskFilter } from "@/Hooks/useTaskFilter";
import TaskFilterBar from "@/Components/molecules/TaskFilterBar";
import TaskList from "@/Components/organisms/TaskList";
import TaskFormModal from "@/Components/organisms/TaskFormModal";
import TaskImportButton from "@/Components/organisms/TaskImportButton";

interface Props {
    tasks: Task[];
    filters: { status: TaskStatus | null };
}

export default function Index({ tasks, filters }: Props) {
    const { currentStatus, setStatusFilter } = useTaskFilter(filters.status);
    const [editingTask, setEditingTask] = useState<Task | null>(null);
    const [modalOpen, setModalOpen] = useState(false);

    const openCreate = () => {
        setEditingTask(null);
        setModalOpen(true);
    };

    const openEdit = (task: Task) => {
        setEditingTask(task);
        setModalOpen(true);
    };

    const handleDelete = (task: Task) => {
        if (confirm(`Usunąć zadanie "${task.title}"?`)) {
            router.delete(`/tasks/${task.id}`, {
                preserveScroll: true,
            });
        }
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        Zadania
                    </h2>
                    <div className="ml-auto flex items-center gap-2">
                        <TaskImportButton />
                        <button
                            onClick={openCreate}
                            className="ml-auto rounded-md bg-slate-800 px-4 py-2 text-sm text-white"
                        >
                            Nowe zadanie
                        </button>
                    </div>
                </div>
            }
        >
            <Head title="Zadania" />

            <div className="mx-auto max-w-7xl space-y-6 p-6">
                <TaskFilterBar
                    current={currentStatus}
                    onChange={setStatusFilter}
                />

                <TaskList
                    key={currentStatus ?? "all"}
                    tasks={tasks}
                    onEdit={openEdit}
                    onDelete={handleDelete}
                />
            </div>

            <TaskFormModal
                task={editingTask}
                open={modalOpen}
                onClose={() => setModalOpen(false)}
            />
        </AuthenticatedLayout>
    );
}
