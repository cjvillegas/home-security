import Orders from './Orders'
import Reports from './Reports'
import Exports from './Exports'
import Scanners from './Scanners'
import ProcessCategory from './ProcessCategory'

// Employee endpoint collections
import EmployeeAuth from './Employee/Auth'
import EmployeeEmployee from './Employee/Employee'

export default {
    Orders,
    Reports,
    Exports,
    Scanners,
    ProcessCategory,

    // employee dedicated endpoints
    EmployeeAuth,
    EmployeeEmployee
}
