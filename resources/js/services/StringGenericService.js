export default class StringGenericService
{
    /**
     * Capitalized all first letter of every word in a string
     *
     * @param str <string>
     * @return <string>
     */
    ucwords(str) {
        if (!str) return ''

        if (!(typeof str === 'string')) return ''

        return (str + '')
            .replace(/^(.)|\s+(.)/g, function ($1) {
                return $1.toUpperCase()
            })
    }

    /**
     * Capitalized first letter of a string
     *
     * @param str <string>
     * @return <string>
     */
    ucfirst(str) {
        if (!(typeof str === 'string')) return ''
        if (!str) return ''

        let lowerCased = str.toLowerCase()

        return `${lowerCased[0].toUpperCase()}${lowerCased.slice(1)}`
    }

    /**
     * Generates a random RGBA string
     *
     * @return string
     */
    getRandomRgba() {
        var o = Math.round, r = Math.random, s = 255;
        return 'rgba(' + o(r()*s) + ',' + o(r()*s) + ',' + o(r()*s) + ',' + r().toFixed(1) + ')';
    }
}
