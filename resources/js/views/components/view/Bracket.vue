<template>
    <div class="view">
        <div class="view-tm-bracket">
            <f-header :name="name" />
            <div class="bracket active">
                <f-bracket :data="data" />
            </div>
        </div>
    </div>
</template>
<script>
import Header from './layout/Header.vue'
import Bracket from '../tournament/Bracket.vue'
export default {
    components: {
        'f-header': Header,
        'f-bracket': Bracket
    },
    props: ['id'],
    data() {
        return {
            name: '',
            data: []
        }
    },
    created() {
        this.fetchData()
    },
    computed: {},
    mounted() {},
    methods: {
        fetchData() {
            axios
                .get('/api/getTournamentBracketData', {
                    params: { id: 1 }
                })
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.name = data.name
                        this.data = data.data
                    }
                }).catch(error => {
                    console.log(error)
                })
        },
    }
}

</script>
