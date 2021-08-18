<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0,0,1920,1003">
        <g class="title no-user-select">
            <g v-for="i in 5" :transform="'translate('+ (i==1 ? (i-1)*340+1 : (i-1)*(340+54.5)) +',0)'">
                <rect rx="10" width="340" height="80" x="0" y="0" stroke-width="0" />
                <text x="170" y="57" text-anchor="middle">{{ levelName(i) }}</text>
            </g>
        </g>
        <g class="player no-user-select">
            <g v-for="i in 5" :transform="'translate('+ (i==1 ? (i-1)*340+1 : (i-1)*(340+54.5)) +',150)'">
                <g :transform="'translate(0, '+ ((i>1 && i<5) ? 280 : 0) +')'" @click="test">
                    <rect rx="10" width="340" height="80" x="0" y="0" />
                    <rect rx="10" width="340" height="80" x="0" y="100" />
                    <path stroke-width="1.5" fill-opacity="null" d="m70 5 v70" />
                    <path stroke-width="1.5" fill-opacity="null" d="m70 105 v70" />
                    <path stroke-width="1.5" fill-opacity="null" d="m270 5 v70" />
                    <path stroke-width="1.5" fill-opacity="null" d="m270 105 v70" />
                    <text x="35" y="55" text-anchor="middle" class="trace">{{ trace(i, 0) }}</text>
                    <text x="35" y="155" text-anchor="middle" class="trace">{{ trace(i, 1) }}</text>
                    <text x="80" y="28" clip-path="url(#clip1)">
                        <tspan class="info">{{ name(i, 0) }}</tspan>
                        <tspan x="80" y="68" class="info">{{ company(i, 0) }}</tspan>
                    </text>
                    <text x="80" y="128" clip-path="url(#clip2)">
                        <tspan class="info">{{ name(i, 1) }}</tspan>
                        <tspan x="80" y="168" class="info">{{ company(i, 1) }}</tspan>
                    </text>
                    <clipPath id="clip1">
                        <rect x="80" y="0" width="180" height="80" />
                    </clipPath>
                    <clipPath id="clip2">
                        <rect x="80" y="100" width="180" height="80" />
                    </clipPath>
                </g>
                <g v-if="!(i>1 && i<5)" :transform="'translate(0, 560)'">
                    <rect rx="10" width="340" height="80" x="0" y="0" />
                    <rect rx="10" width="340" height="80" x="0" y="100" />
                    <path stroke-width="1.5" fill-opacity="null" d="m70 5 v70" />
                    <path stroke-width="1.5" fill-opacity="null" d="m70 105 v70" />
                    <path stroke-width="1.5" fill-opacity="null" d="m270 5 v70" />
                    <path stroke-width="1.5" fill-opacity="null" d="m270 105 v70" />
                    <text x="35" y="55" text-anchor="middle" class="trace">{{ trace(i, 2) }}</text>
                    <text x="35" y="155" text-anchor="middle" class="trace">{{ trace(i, 3) }}</text>
                    <text x="80" y="28" clip-path="url(#clip1)">
                        <tspan class="info">{{ name(i, 2) }}</tspan>
                        <tspan x="80" y="68" class="info">{{ company(i, 2) }}</tspan>
                    </text>
                    <text x="80" y="128" clip-path="url(#clip2)">
                        <tspan class="info">{{ name(i, 3) }}</tspan>
                        <tspan x="80" y="168" class="info">{{ company(i, 3) }}</tspan>
                    </text>
                </g>
            </g>
        </g>
        <g class="line">
            <path stroke-width="2" fill-opacity="0" d="M343 240 h25 v560 h-25" />
            <path stroke-width="2" fill-opacity="0" d="M368 520 h25" />
            <path stroke-width="2" fill-opacity="0" d="M738 473 h25 v100 h-25" />
            <path stroke-width="2" fill-opacity="0" d="M763 520 h25" />
            <path stroke-width="2" fill-opacity="0" d="M1130 520 h25" />
            <path stroke-width="2" fill-opacity="0" d="M1181 473 h-25 v100 h25" />
            <path stroke-width="2" fill-opacity="0" d="M1523 520 h25" />
            <path stroke-width="2" fill-opacity="0" d="M1573 240 h-25 v560 h25" />
        </g>
    </svg>
</template>
<script>
export default {
    components: {},
    props: ['data'],
    data() {
        return {}
    },
    created() {},
    computed: {},
    mounted() {},
    methods: {
        levelName(level) {
            var name = 'Round 1'
            switch (level) {
                case 1:
                    name += " A"
                    break
                case 2:
                    name = "Semi-final A"
                    break
                case 3:
                    name = "Final"
                    break
                case 4:
                    name = "Semi-final B"
                    break
                default:
                    name += " B"
                    break
            }
            return name;
        },
        trace(i, type) {
            var trace = ''
            switch (i) {
                case 1:
                case 5:
                    trace = i + type
                    break
            }
            return trace
        },
        name(i, type) {
            var name = ''
            switch (i) {
                case 1:
                case 5:
                    name = this.data.filter(item => {
                        return item.track === i + type
                    })
                    if (name.length) {
                        name = name[0].u_name.split(" 1")
                        name = name[0].split("(")
                        name = name[0]
                    } else {
                        name = ''
                    }
                    break;
            }
            return name;
        },
        company(i, type) {
            var name = ''
            switch (i) {
                case 1:
                case 5:
                    name = this.data.filter(item => {
                        return item.track === i + type
                    })
                    if (name.length) {
                        name = name[0].p_name.split(" ")
                        name = name[1] ? name[1] : name[0]
                    } else {
                        name = ''
                    }
                    break;
            }
            return name;
        },
        test() {
            console.log('ok')
        }
    }
}

</script>
