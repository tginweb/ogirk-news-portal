export default {

    data() {
        return {
            routeFilter: {
                date: this.$route.params.date,
            },
            routeNav: {
                next: this.$route.query.next,
                previous: this.$route.query.previous,
            }
        }
    },
    watch: {
        $route(route, before) {

           if (route.query.next) {
                this.routeNav.next = route.query.next
                this.routeNav.previous = null
            }
            if (route.query.previous) {
                this.routeNav.previous = route.query.previous
                this.routeNav.next = null
            }

            this.routeFilter.date = route.params.date
        }
    },

    computed: {
        routePaginated() {
           return this.$route.query.next || this.$route.query.previous
        }
    }
}
