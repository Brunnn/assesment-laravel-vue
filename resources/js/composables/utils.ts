import _ from "lodash";

export function useUtils(){
    function studlyCase(str: string) {

        //Replace underscored or dots with spaces
        str = str.replace(/_|\.|\-/g, " ");
        
        return _.startCase(str);
    }
    return {
        studlyCase
    }
}