export interface GenericFilterModelCondition {
    filterType: "text" | "number" | "date";
    value?: string | number | Date;
    operation: "equals" | "greaterThan" | "lessThan" | "contains";
}
export interface GenericFilterModel {
    column: string;
    junction?: "and" | "or";
    conditions: GenericFilterModelCondition[];
}

/**
 * This composable is responsible for handling the backend's generic filtering model that is used in the URL query string.
 */
export function useGenericFilter() {
    function validOperationsForType(
        type: "text" | "number" | "date"
    ): { label: string; value: string }[] {
        switch (type) {
            case "text":
                return [
                    {
                        label: "Equals",
                        value: "equals",
                    },
                    {
                        label: "Contains",
                        value: "contains",
                    },
                ];
            case "number":
                return [
                    {
                        label: "Equals",
                        value: "equals",
                    },
                    {
                        label: "Greater than",
                        value: "greaterThan",
                    },
                    {
                        label: "Less than",
                        value: "lessThan",
                    },
                ];
            case "date":
                return [
                    {
                        label: "Equals",
                        value: "equals",
                    },
                    {
                        label: "Greater than",
                        value: "greaterThan",
                    },
                    {
                        label: "Less than",
                        value: "lessThan",
                    },
                ];
            default:
                return [];
        }
    }

    return {
        validOperationsForType,
    };
}
