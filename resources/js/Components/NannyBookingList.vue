<script lang="ts" setup generic="T extends Array<any>">
import { ref } from "vue";
import _ from "lodash";
import { onMounted } from "vue";
import { PlusIcon, XMarkIcon } from "@heroicons/vue/24/solid";
import NannyBookingCard from "./NannyBookingCard.vue";
import { NannyBooking } from "../types/schema";
import axios from "axios";
import {
    GenericFilterModel,
    useGenericFilter,
} from "../composables/genericFilter";
import { useUtils } from "../composables/utils";

const { studlyCase } = useUtils();
const { validOperationsForType } = useGenericFilter();
const props = defineProps<{
    dataset: NannyBooking[];
}>();
async function fillNannyBookings(
    search: string = null,
    filters: GenericFilterModel[]
) {
    axios
        .get("/api/nanny-bookings/filter", {
            params: {
                search: search,
                filters: filters
                    ? encodeURI(JSON.stringify(filters))
                    : undefined,
            },
        })
        .then((response) => {
            renderedResults.value = response.data;
        });
}

const fullTextSearchHandler = _.debounce((e: Event) => {
    fillNannyBookings((e.target as HTMLInputElement).value, null);
}, 1000);

const applyFilters = _.debounce(() => {
    fillNannyBookings(null, columnFilters.value);
}, 1000);

const renderedResults = ref<NannyBooking[]>([]);

onMounted(async () => {
    renderedResults.value = props.dataset;

    //We get the filterable columns
    axios.get("/api/nanny-bookings/filter/columns").then((response) => {
        possibleColumns.value = response.data;
    });
});

const possibleColumns = ref<string[]>([]);
const columnFilters = ref<GenericFilterModel[]>([]);
async function addColumnFilter() {
    columnFilters.value.push({
        column: undefined,
        conditions: [
            {
                filterType: "text",
                operation: "greaterThan",
                value: undefined,
            },
        ],
        junction: "and",
    });
}
</script>
<template>
    <div class="filters">
        <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    type="text"
                    @input="fullTextSearchHandler"
                    placeholder="Search for something..."
                />
            </div>

            <div class="column-filter-wrapper">
                <PlusIcon @click="addColumnFilter" class="w-6 h-6 cursor-pointer"></PlusIcon>

                <div
                    class="column-filter relative"
                    v-for="columnFilter in columnFilters"
                >
                    <XMarkIcon
                        class="w-6 h-6 absolute right-0 top-0 cursor-pointer"
                        @click="
                            columnFilters.splice(
                                columnFilters.indexOf(columnFilter),
                                1
                            )
                        "
                    ></XMarkIcon>
                    <div class="w-full">
                        <div class=" flex flex-wrap -mx-3 mb-6">
                            <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                                <label
                                    class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                    for="grid-state"
                                >
                                    Column
                                </label>
                                <div class="relative">
                                    <select
                                        v-model="columnFilter.column"
                                        class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    >
                                        <option
                                            :value="undefined"
                                            disabled
                                            selected
                                        >
                                            Select a column
                                        </option>
                                        <option
                                            v-for="column in possibleColumns"
                                            :value="column"
                                        >
                                            {{ studlyCase(column) }}
                                        </option>
                                    </select>

                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                                    >
                                        <svg
                                            class="fill-current h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full md:w-1/4 px-3">
                                <label
                                    class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                    for="grid-last-name"
                                >
                                    Filter
                                </label>
                                <input
                                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    id="grid-last-name"
                                    type="text"
                                    v-model="columnFilter.conditions[0].value"
                                    placeholder="Filter here"
                                />
                            </div>
                            <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                                <label
                                    class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                    for="grid-state"
                                >
                                    Filter Type
                                </label>
                                <div class="relative">
                                    <select
                                        v-model="
                                            columnFilter.conditions[0]
                                                .filterType
                                        "
                                        class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    >
                                        <option
                                            :value="undefined"
                                            disabled
                                            selected
                                        >
                                            Select the filter type
                                        </option>
                                        <option value="text">Text</option>
                                        <option value="number">Number</option>
                                        <option value="date">Date</option>
                                    </select>

                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                                    >
                                        <svg
                                            class="fill-current h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                                <label
                                    class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                    for="grid-state"
                                >
                                    Operation
                                </label>
                                <div class="relative">
                                    <select
                                        v-model="
                                            columnFilter.conditions[0].operation
                                        "
                                        class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    >
                                        <option
                                            :value="undefined"
                                            disabled
                                            selected
                                        >
                                            Select the operation
                                        </option>
                                        <option
                                            v-for="operation in validOperationsForType(
                                                columnFilter.conditions[0]
                                                    .filterType
                                            )"
                                            :value="operation.value"
                                        >
                                            {{ operation.label }}
                                        </option>
                                    </select>

                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                                    >
                                        <svg
                                            class="fill-current h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button
                    v-if="columnFilters.length > 0"
                    @click="applyFilters"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="button"
                >
                    Aplicar Filtros
                </button>
            </div>
        </form>

        <div class="nanny-bookins-wrapper w-full flex flex-row flex-wrap p-2">
            <NannyBookingCard
                :nannyBooking="nannyBooking"
                v-for="nannyBooking in renderedResults"
                :key="nannyBooking.id"
            >
            </NannyBookingCard>
        </div>
    </div>
</template>
<style lang="scss" scoped></style>
