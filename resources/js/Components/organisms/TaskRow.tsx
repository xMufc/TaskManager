import {
    Task,
    TaskStatus,
    STATUS_LABELS,
    STATUS_STYLES,
    PRIORITY_LABELS,
    PRIORITY_STYLES,
} from "@/types/task";
import TaskStatusSelect from "../molecules/TaskStatusSelect";
import Badge from "@/Components/atoms/Badge";
import { useState } from "react";

interface Props {
    task: Task;
    isPending: boolean;
    onStatusChange: (status: TaskStatus) => void;
    onEdit: () => void;
    onDelete: () => void;
}

export default function TaskRow({
    task,
    isPending,
    onStatusChange,
    onEdit,
    onDelete,
}: Props) {
    const canEdit = task.status !== "done" && task.status !== "cancelled";
    const [descriptionExpanded, setDescriptionExpanded] = useState(false);

    const description = task.description ?? "";
    const shouldTruncate = description.length > 150;

    const visibleDescription =
        !descriptionExpanded && shouldTruncate
            ? `${description.slice(0, 150)}...`
            : description;
    return (
        <tr className={isPending ? "opacity-50" : ""}>
            <td className="px-4 py-3">
                <div className="font-medium text-slate-900">{task.title}</div>

                {description && (
                    <div className="text-justify mt-1 text-sm text-slate-500">
                        <span className="">{visibleDescription}</span>

                        {shouldTruncate && (
                            <button
                                type="button"
                                onClick={() =>
                                    setDescriptionExpanded(
                                        (expanded) => !expanded,
                                    )
                                }
                                className="ml-1 text-blue-600 hover:underline"
                            >
                                {descriptionExpanded ? "Zwiń" : "Rozwiń"}
                            </button>
                        )}
                    </div>
                )}
            </td>

            <td className="px-4 py-3">
                <Badge className={STATUS_STYLES[task.status]}>
                    {STATUS_LABELS[task.status]}
                </Badge>
            </td>

            <td className="px-4 py-3">
                <Badge className={PRIORITY_STYLES[task.priority]}>
                    {PRIORITY_LABELS[task.priority]}
                </Badge>
            </td>

            <td className="px-4 py-3 text-sm text-slate-600">
                {task.dueDate ?? "-"}
            </td>

            <td className="px-4 py-3">
                <TaskStatusSelect
                    task={task}
                    disabled={isPending}
                    onChange={onStatusChange}
                />
            </td>

            <td className="px-4 py-3 text-right">
                <div className="flex justify-end gap-3">
                    {canEdit && (
                        <button
                            type="button"
                            onClick={onEdit}
                            className="text-sm text-blue-600 hover:underline"
                        >
                            Edytuj
                        </button>
                    )}

                    <button
                        type="button"
                        onClick={onDelete}
                        className="text-sm text-rose-600 hover:underline"
                    >
                        Usuń
                    </button>
                </div>
            </td>
        </tr>
    );
}
