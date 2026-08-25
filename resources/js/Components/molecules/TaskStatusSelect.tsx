import { Task, TaskStatus, STATUS_LABELS } from "@/types/task";
import Select from "@/Components/atoms/Select";

interface Props {
    task: Task;
    disabled?: boolean;
    onChange: (status: TaskStatus) => void;
}

export default function TaskStatusSelect({ task, disabled, onChange }: Props) {
    const { status, allowedNextStatuses } = task;

    if (allowedNextStatuses.length === 0) {
        return null;
    }

    const statuses = [status, ...allowedNextStatuses].filter(
        (status, index, allStatuses) => allStatuses.indexOf(status) === index,
    );

    return (
        <Select
            value={status}
            disabled={disabled}
            onChange={(event) => onChange(event.target.value as TaskStatus)}
        >
            {statuses.map((status) => (
                <option key={status} value={status}>
                    {STATUS_LABELS[status]}
                </option>
            ))}
        </Select>
    );
}
