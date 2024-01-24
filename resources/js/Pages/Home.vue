<script lang="ts" setup>
import { useDate } from "../composables/date";
import { NannyBooking } from "../types/schema";

const props = defineProps<{
    nannyBookings: NannyBooking[];
}>();

const { ToDate } = useDate();
</script>
<template>
    <div>
        <h4 class="p-4 text-2xl font-semibold text-gray-950">
            Nanny Bookings
        </h4>
        <div class="nanny-bookins-wrapper w-full flex flex-row flex-wrap p-2">
            <div
                v-for="nannyBooking in props.nannyBookings"
                :key="nannyBooking.id"
                class="w-1/3 p-1"
            >
                <div
                    class="card p-3 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                >
                    <a href="#">
                        <h5
                            class=" mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white w-full text-ellipsis overflow-hidden whitespace-nowrap"
                        >
                            Booking: {{ nannyBooking.title }}
                        </h5>
                    </a>
                    <p
                        class="mb-3 font-normal text-gray-700 dark:text-gray-400"
                    >
                        {{ nannyBooking.price }}
                    </p>
                    <p class="mb-4 font-normal text-gray-700 dark:text-gray-400 w-full text-ellipsis overflow-hidden whitespace-nowrap">
                        {{ ToDate(nannyBooking.start_at) }} until {{ ToDate(nannyBooking.end_at) }}
                    </p>
                    <p class="mb-4 font-normal text-gray-700 dark:text-gray-400  w-full text-ellipsis overflow-hidden whitespace-nowrap">
                        {{ nannyBooking.user.name }} - {{ nannyBooking.user.email }}
                    </p>
                    
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss" scoped></style>
