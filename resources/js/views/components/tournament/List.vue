<template>
    <div>
        <Breadcrumb :iteams="br" />
        <el-row type="flex" justify="end" style="margin-bottom: 15px">
            <el-col :span="6" style="text-align: right;">
                <el-button class="hidden-sm-and-down" type="success" round @click="addTournament">Add New tournament</el-button>
                <el-button class="hidden-md-and-up" type="success" icon="el-icon-plus" circle @click="addTournament"></el-button>
            </el-col>
        </el-row>
        <br>
        <el-table :data="tableData" border :empty-text="$store.state.langData.cont.msg.data.d0001" style="width: 100%">
            <el-table-column type="index" width="50">
            </el-table-column>
            <el-table-column prop="id" :label="$store.state.langData.cont.pageFn.table.Id">
            </el-table-column>
            <el-table-column prop="name" :label="$store.state.langData.cont.pageFn.table.Name">
            </el-table-column>
            <el-table-column prop="createTime" :label="$store.state.langData.cont.pageFn.table.createTime">
            </el-table-column>
            <el-table-column fixed="right" :label="$store.state.langData.cont.pageFn.table.Operating" width="100">
                <template slot-scope="scope">
                    <el-button @click.native.prevent="handleEdit(scope.$index, scope.row)" type="text">
                        {{ $store.state.langData.cont.pageFn.table.Edit }}
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>
<script>
import Breadcrumb from '../layout/Breadcrumb.vue'
export default {
    components: { Breadcrumb },
    props: [],
    data() {
        return {
            br: [{
                title: this.$store.state.langData.cont.slideMenu.Tournament,
                isUrl: false
            }, {
                title: this.$store.state.langData.cont.slideMenu.List,
                isUrl: false
            }],
            tableData: []
        }
    },
    created() {
        this.fetchData()
    },
    methods: {
        addTournament() {
            this.$router.push({ name: 'AddTournament' })
        },
        fetchData() {
            axios
                .get('/api/getTournamentListData')
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.tableData = data.data
                    }
                }).catch(error => {
                    console.log(error)
                })
        },
        handleEdit(index, row) {
            this.$router.push({ name: 'EditTournament', query: { id: row.id } })
        }
    }
}

</script>
