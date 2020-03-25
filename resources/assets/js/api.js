import axios from 'axios';

const URL_MATCHES = '{{ url("api/match") }}',
    URL_MATCH = '{{ url("api/match") }}',
    URL_MOVE = '{{ url("api/match") }}',
    URL_CREATE = '{{ url("api/match") }}',
    URL_DELETE = '{{ url("api/match") }}';

export default {
    matches: () => {
        return axios.get(URL_MATCHES)
    },
    match: ({id}) => {
        return axios.get(URL_MATCH + id)
    },
    move: ({id, position}) => {
        return axios.put(URL_MOVE + id, {
            position: position
        })
    },
    create: () => {
        return axios.post(URL_CREATE)
    },
    destroy: ({id}) => {
        return axios.delete(URL_DELETE + id)
    },
}
