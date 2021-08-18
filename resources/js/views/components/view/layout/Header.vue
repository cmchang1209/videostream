<template>
    <div class="header">
        <div class="h_left">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0,0,572,77">
                <image xlink:href="../../../../../sass/images/view_logo.png" height="100%" />
                <text x="100" y="60" font-size="48" fill="#fff" letter-spacing="3">FIDO ONLINE</text>
            </svg>
        </div>
        <div class="h_center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0,0,753,77">
                <text x="50%" y="60" text-anchor="middle" font-size="48" fill="#fff" letter-spacing="3">
                    {{ name }}
                </text>
            </svg>
        </div>
        <div class="h_right">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0,0,572,77">
                <text x="571" y="60" text-anchor="end" font-size="48" fill="#fff" letter-spacing="3">
                    {{ today }}
                </text>
            </svg>
        </div>
    </div>
</template>
<script>
export default {
    components: {},
    props: ['name'],
    sockets: {},
    data() {
        return {}
    },
    created() {},
    computed: {
        today() {
            return (new Date()).toString().split(' ').splice(1, 3).join(' ')
        }
    },
    methods: {}
}

</script>
