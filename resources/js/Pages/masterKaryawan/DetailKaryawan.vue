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

const activeTab = ref('Absensi')

const breadcrumbs = [
  { label: 'Dashboard', url: '/panel' },
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
  job_class: props.karyawan?.job_class ?? '',
  tgl_efektif: props.karyawan?.tgl_efektif ?? '',
  tgl_tetap: props.karyawan?.tgl_tetap ?? '',
  tgl_keluar: props.karyawan?.tgl_keluar ?? '',
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
                <div class="col-auto"><br>
                    <Breadcrumb :items="breadcrumbs" />
                </div>
            </div>
        </div>
        <!-- END PAGE HEADER -->

    <div class="page-body">
        <div class="container-fluid">
            <div class="row row-deck row-cards">
                <!-- <div class="col-lg-8"> -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <!-- FOTO -->
                                <div class="col-md-2 text-center">
                                <span class="avatar avatar-xl mb-3"
                                    :style="`background-image: url('/images/default.png')`">
                                </span>
                                <h3 class="mb-0">{{ form.nama }}</h3>
                                <div class="text-muted">{{ form.jabatan }}</div>
                                <span 
                                    class="badge mt-2"
                                    :class="form.status_karyawan === 'AKTIF' 
                                    ? 'bg-success-lt' 
                                    : 'bg-danger-lt'">
                                    {{ form.status_karyawan }}
                                </span>
                                </div>

                                <!-- DATA DETAIL -->
                                <div class="col-md-5">

                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">NIK</div>
                                        <div class="col-md-6 fw-medium"> : {{ form.nik }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">NIK Lama</div>
                                        <div class="col-md-6"> : {{ form.nik_lama }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">Nama</div>
                                        <div class="col-md-6"> : {{ form.nama }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">Gudang</div>
                                        <div class="col-md-6"> : {{ form.gudang }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">Bagian</div>
                                        <div class="col-md-6"> : {{ form.bagian }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">Kelas</div>
                                        <div class="col-md-6"> : {{ form.kelas }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">Jabatan</div>
                                        <div class="col-md-6"> : {{ form.jabatan }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">Status Kerja</div>
                                        <div class="col-md-6"> :
                                        <span class="badge bg-blue-lt">
                                            {{ form.status_kerja }}
                                        </span>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-5">

                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">Tipe</div>
                                        <div class="col-md-6 fw-medium"> : {{ form.tipe }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">Status Karyawan</div>
                                        <div class="col-md-6"> : {{ form.status_karyawan }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">Job Class</div>
                                        <div class="col-md-6"> : {{ form.job_class }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">Tanggal Efektif</div>
                                        <div class="col-md-6"> : {{ form.tgl_efektif }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">Tanggal Tetap</div>
                                        <div class="col-md-6"> : {{ form.tgl_tetap }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">Tanggal Keluar</div>
                                        <div class="col-md-6"> : {{ form.tgl_keluar }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">Ket Masuk</div>
                                        <div class="col-md-6"> : {{ form.ket_masuk }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">Ket Keluar</div>
                                        <div class="col-md-6"> : {{ form.ket_keluar }}</div>
                                    </div>

                                    

                                </div>
                            </div>
                        </div>
                    </div>
                <!-- </div> -->

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs nav-tabs-alt card-header-tabs">
                                <li class="nav-item">
                                    <button
                                    class="nav-link"
                                    :class="{ active: activeTab === 'Absensi' }"
                                    @click="activeTab = 'Absensi'"
                                    >
                                    Data Absensi
                                    </button>
                                </li>

                                <li class="nav-item">
                                    <button
                                    class="nav-link"
                                    :class="{ active: activeTab === 'pekerjaan' }"
                                    @click="activeTab = 'pekerjaan'"
                                    >
                                    Data Pekerjaan
                                    </button>
                                </li>

                                <li class="nav-item">
                                    <button
                                    class="nav-link"
                                    :class="{ active: activeTab === 'dokumen' }"
                                    @click="activeTab = 'dokumen'"
                                    >
                                    Dokumen
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div v-if="activeTab === 'Absensi'">
                            <h5>Data Absensi</h5>
                            Isi data Absensi di sini
                            </div>

                            <div v-if="activeTab === 'pekerjaan'">
                            <h5>Data Pekerjaan</h5>
                            Isi data pekerjaan di sini
                            </div>

                            <div v-if="activeTab === 'dokumen'">
                            <h5>Dokumen</h5>
                            Isi dokumen di sini
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</template>