import Orders from './Orders'
import Reports from './Reports'
import Exports from './Exports'
import Scanners from './Scanners'
import Processes from './Processes'
import ProcessCategory from './ProcessCategory'
import Machine from './Machine'

// Employee endpoint collections
import EmployeeAuth from './Employee/Auth'
import EmployeeEmployee from './Employee/Employee'

export default {
    Orders,
    Reports,
    Exports,
    Processes,
    ProcessCategory,
    Machine,
    Scanners,

    // employee dedicated endpoints
    EmployeeAuth,
    EmployeeEmployee,
}
