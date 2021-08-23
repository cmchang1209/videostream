<template>
    <div class="footer">
        <div :class="['f_left', left ? 'active' : '']" @click="changShowModel('home')">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0,0,382,84">
                <text x="20" y="50" font-size="42" letter-spacing="3">{{ data[0].track }}</text>
                <text x="10" y="70" font-size="16" letter-spacing="3">{{ uId(data[0].u_name) }}</text>
                <text x="362" y="46" font-size="32" text-anchor="end" letter-spacing="3">{{ uName(data[0].u_name) }}</text>
                <text x="372" y="70" font-size="16" text-anchor="end" letter-spacing="3">{{ data[0].p_name }}</text>
            </svg>
        </div>
        <div class="f_center" @click="changShowModel('tree')">
        </div>
        <div :class="['f_right', right ? 'active' : '']" @click="changShowModel('away')">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0,0,382,84">
                <text x="20" y="50" font-size="42" letter-spacing="3">{{ data[1].track }}</text>
                <text x="20" y="70" font-size="16" letter-spacing="3">{{ uId(data[1].u_name) }}</text>
                <text x="362" y="46" font-size="32" text-anchor="end" letter-spacing="3">{{ uName(data[1].u_name) }}</text>
                <text x="372" y="70" font-size="16" text-anchor="end" letter-spacing="3">{{ data[1].p_name }}</text>
            </svg>
        </div>
    </div>
</template>
<script>
export default {
    components: {},
    props: ['data'],
    sockets: {},
    data() {
        return {
            left: true,
            right: false
        }
    },
    created() {},
    computed: {},
    methods: {
        changShowModel(type) {
            switch (type) {
                case 'home':
                    this.left = true
                    this.right = false
                    break
                case 'away':
                    this.left = false
                    this.right = true
                    break
                default:
                    this.left = false
                    this.right = false
            }
            this.$emit('changShow', type);
        },
        uId(name) {
            var ns = name.split(" ")
            return ns[1]
        },
        uName(name) {
            var ns = name.split(" ")
            return ns[0]
        }
    }
}

</script>
