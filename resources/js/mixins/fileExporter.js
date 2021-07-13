export default {
    methods: {
        exporter(type, name, data, appendDate = true) {
            let baseName = appendDate ? name + ' - '+ moment().format('YYYY-MM-DD') : name
            let fileName = baseName + '.'+ type
            const url = window.URL.createObjectURL(new Blob([data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', fileName);
            document.body.appendChild(link);
            link.click();
        }
    }
}
