<script setup>
import Layout from '../../Layout.vue'
import { useDataTable } from '@/Composables/useDataTable'
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { reactive } from 'vue'

import Breadcrumb from '@/Components/Breadcrumb.vue'

defineOptions({
  layout: Layout
})

const props = defineProps({
  karyawan: Object
})

const breadcrumbs = [
  { label: 'Dashboard', url: '/panel' },
  { label: 'Karyawan', url: '/masterKaryawan' },
  { label: 'Data Karyawan', url: '/masterKaryawan' },
  { label: 'Detail Karyawan', url: 'detailKaryawan' },
]

const form = reactive({
  nik: props.karyawan?.nik ?? '',
  nik_lama: props.karyawan?.nik_lama ?? '',
  nama: props.karyawan?.nama ?? '',
  gudang: props.karyawan?.gudang ?? '',
  bagian: props.karyawan?.bagian ?? '',
  kelas: props.karyawan?.kelas ?? '',
  jabatan: props.karyawan?.jabatan ?? '',
  status_kerja: props.karyawan?.status_kerja ?? '',
  status_karyawan: props.karyawan?.status_karyawan ?? '',
  tipe: props.karyawan?.tipe ?? '',
  jobclass: props.karyawan?.jobclass ?? '',
  ket_masuk: props.karyawan?.ket_masuk ?? '',
  ket_keluar: props.karyawan?.ket_keluar ?? '',
})




</script>

<template>
    <!-- <Layout> -->
        <Head title="Master Karyawan" />
        <!-- BEGIN PAGE HEADER -->
        <div class="page-header d-print-none" aria-label="Page header">
            <div class="container-fluid">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h2 class="page-title">Detail Karyawan</h2>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE HEADER -->

    <div class="page-body">
        <div class="container-fluid">
            <div class="col-auto">
                <Breadcrumb :items="breadcrumbs" /><br>
            </div>
            <div class="row row-deck row-cards">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title mb-0">Data Karyawan</h3>

                            <div class="ms-auto d-flex gap-2">
                                <a href="#"
                                class="btn btn-sm btn-warning"
                                @click="resetForm">
                                    Reset
                                </a>

                                <a href="#"
                                class="btn btn-sm btn-primary"
                                @click.prevent="showModal">
                                    <i class="fa-solid fa-plus"></i> Buat Baru
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row align-items-stretch">

                                <!-- KOLOM KIRI -->
                                <div class="col-md-4 d-flex">
                                    <div class="border w-100 d-flex justify-content-center align-items-center text-secondary">
                                        <svg viewBox="0 0 24 24" width="70%" height="70%" fill="gray">
                                            <circle cx="12" cy="7" r="4"></circle>
                                            <path d="M4 21c0-4 4-6 8-6s8 2 8 6"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- KOLOM KANAN -->
                                <div class="col-md-8 d-flex">
                                    <div class="w-100">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">NIK</label>
                                                <input type="text" v-model="form.nik" class="form-control" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">NIK Lama</label>
                                                <input type="text" v-model="form.nik_lama" class="form-control">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-8">
                                                <label class="form-label">Nama</label>
                                                <input type="text" v-model="form.nama" @input="form.nama = form.nama.toUpperCase()" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Gudang</label>
                                                <select v-model="form.gudang" class="form-select">
                                                    <option value="" disabled>-Pilih Gudang-</option>
                                                    <option value="DCI">DCI</option>
                                                    <option value="DEPO 1">DEPO 1</option>
                                                    <option value="DEPO 2">DEPO 2</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Bagian</label>
                                                <select v-model="form.bagian" class="form-select">
                                                    <option value="" disabled>-Pilih Bagian-</option>
                                                    <option value="ADMIN">ADMIN</option>
                                                    <option value="RECEIVING">RECEIVING</option>
                                                    <option value="RETUR">RETUR</option>
                                                    <option value="PERISHABLE">PERISHABLE</option>
                                                    <option value="WAREHOUSE">WAREHOUSE</option>
                                                    <option value="WAREHOUSEKLIK">WAREHOUSE KLIK</option>
                                                    <option value="ISSUINGDELVIERY">ISSUING DELIVERY</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Kelas</label>
                                                <select v-model="form.kelas" class="form-select">
                                                    <option value="" disabled>-Pilih Kelas-</option>
                                                    <option value="SUPERVISOR">SUPERVISOR</option>
                                                    <option value="SR. CLERK">SR. CLERK</option>
                                                    <option value="CLERK">CLERK</option>
                                                    <option value="COORDINATOR">COORDINATOR</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Jabatan</label>
                                                <select v-model="form.jabatan"
                                                    class="form-select"
                                                    :disabled="form.bagian === ''">

                                                    <option value="" disabled>-Pilih Jabatan-</option>

                                                    <option 
                                                        v-for="item in jabatanOptions" 
                                                        :key="item" 
                                                        :value="item">
                                                        {{ item }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="row"> -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Status Kerja</label>
                                        <select v-model="form.status_kerja" class="form-select">
                                            <option value="" disabled>-Pilih Status Kerja-</option>
                                            <option>TETAP</option>
                                            <option>KONTRAK</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Status Karyawan</label>
                                        <select v-model="form.status_karyawan" class="form-select">
                                            <option value="" disabled>-Pilih Status Karyawan-</option>
                                            <option>AKTIF</option>
                                            <option>TIDAK AKTIF</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Tipe</label>
                                        <select v-model="form.tipe" class="form-select">
                                            <option value="" disabled>-Pilih Tipe-</option>
                                            <option>DRIVER</option>
                                            <option>NON DRIVER</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Jobclass</label>
                                        <select v-model="form.jobclass" class="form-select">
                                            <option value="" disabled>-Pilih Jobclass-</option>
                                            <option>A1</option>
                                            <option>A2</option>
                                            <option>A3</option>
                                            <option>B1</option>
                                            <option>B2</option>
                                            <option>B3</option>
                                            <option>C1</option>
                                            <option>C2</option>
                                            <option>C3</option>
                                        </select>
                                    </div>
                                </div>
                            <!-- </div> -->
                            <!-- <div class="row"> -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Tanggal Efektif</label>
                                        <input type="date" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tanggal Tetap</label>
                                        <input type="date" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tanggal Keluar</label>
                                        <input type="date" class="form-control">
                                    </div>
                                </div>
                            <!-- </div> -->
                            <!-- <div class="row"> -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Keterangan Masuk</label>
                                        <textarea v-model="form.ket_masuk" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Keterangan Keluar</label>
                                        <textarea v-model="form.ket_keluar" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            <!-- </div> -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="h2">Master Karyawan</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>