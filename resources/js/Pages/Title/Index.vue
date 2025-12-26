<template>
    <div class="card shadow-sm">

        <!-- 🔹 Header -->
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Title Management</h4>
            <a href="/create" class="btn btn-primary btn-sm">
                + Create New
            </a>
        </div>

        <!-- 🔹 Table -->
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="20%">Title</th>
                        <th>Description</th>
                        <th width="15%">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="item in titles" :key="item.id">
                        <td>{{ item.id }}</td>
                        <td class="fw-semibold">{{ item.title }}</td>

                        <!-- Render TinyMCE HTML -->
                        <td v-html="item.description"></td>

                        <td>
                            <a
                                :href="`/edit/${item.id}`"
                                class="btn btn-warning btn-sm me-1"
                            >
                                Edit
                            </a>

                            <a
                                :href="`/delete/${item.id}`"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete?')"
                            >
                                Delete
                            </a>
                        </td>
                    </tr>

                    <tr v-if="titles.length === 0">
                        <td colspan="4" class="text-center text-muted py-4">
                            No records found
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</template>

<script>
export default {
    data() {
        const el = document.getElementById('app')
        return {
            titles: JSON.parse(el.dataset.titles || '[]')
        }
    }
}
</script>
