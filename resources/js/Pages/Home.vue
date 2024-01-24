<script lang="ts" setup>
import { ref } from "vue";
import { NannyBooking } from "../types/schema";
import NannyBookingList from "../Components/NannyBookingList.vue";

const props = defineProps<{
    nannyBookings: NannyBooking[];
}>();

</script>
<template>
    <div>
        <h4 class="p-4 text-2xl font-semibold text-gray-950">Nanny Bookings</h4>
       
        <NannyBookingList :dataset="nannyBookings"></NannyBookingList>

    </div>
</template>
<style lang="scss" scoped></style>
