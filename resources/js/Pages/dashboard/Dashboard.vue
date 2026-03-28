<script setup>
import Layout from '../../LayoutUser.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

defineProps({ user: Object })

const page = usePage()

const user = computed(() => page.props.auth.user)
const absensi = computed(() => page.props.absensi || [])

defineOptions({
  layout: Layout
})

</script>

<template>
    <Head title="Dashboard" />
    <!-- BEGIN PAGE HEADER -->
        <div class="page-header d-print-none" aria-label="Page header">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <!-- Page pre-title -->
                        <div class="page-pretitle">Overview</div>
                        <h2 class="page-title">Halo, {{ user?.nama }}</h2>
                        <p>NIK: {{ user?.nik }}</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE HEADER -->

        <!-- BEGIN PAGE BODY -->
        <div class="page-body">
            <div class="container-xl">
                <div class="card mt-3">
                    <div class="card-header">
                    <h3 class="card-title">Data Absensi</h3>
                    </div>

                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Shift</th>
                                    <th>Normal In</th>
                                    <th>Machine In</th>
                                    <th>Terlambat</th>
                                    <th>Normal Out</th>
                                    <th>Machine Out</th>
                                    <th>Pulang Cepat</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in absensi" :key="item.tanggal">
                                    <td>{{ item.tanggal }}</td>
                                    <td>{{ item.shiftcode }}</td>

                                    <td>{{ item.normal_in }}</td>
                                    <td>{{ item.machine_in }}</td>

                                    <td>{{ item.terlambat }}</td>

                                    <td>{{ item.normal_out }}</td>
                                    <td>{{ item.machine_out }}</td>

                                    <td>{{ item.pulang_cepat }}</td>

                                    <td>
                                        <span 
                                            class="badge"
                                            :class="{
                                                'bg-danger': item.status === 'MANGKIR',
                                                'bg-warning': item.status === 'TELAT',
                                                'bg-info': item.status === 'PULANG CEPAT',
                                                'bg-success': item.status === 'NORMAL'
                                            }"
                                        >
                                            {{ item.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</template>