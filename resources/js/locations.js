import { provinces, districts, sectors, cells, villages } from 'rwanda-geo';

// Make data available globally
window.RwandaGeo = {
    provinces: provinces,
    districts: districts,
    sectors: sectors,
    cells: cells,
    villages: villages,
    
    getDistrictsByProvince: function(provinceCode) {
        return this.districts.filter(d => d.province_code === provinceCode);
    },
    
    getSectorsByDistrict: function(districtCode) {
        return this.sectors.filter(s => s.district_code === districtCode);
    },
    
    getCellsBySector: function(sectorCode) {
        return this.cells.filter(c => c.sector_code === sectorCode);
    },
    
    getVillagesByCell: function(cellCode) {
        return this.villages.filter(v => v.cell_code === cellCode);
    }
};

console.log('Rwanda Geo loaded successfully!');
console.log('Provinces:', provinces.length);
console.log('Districts:', districts.length);
console.log('Sectors:', sectors.length);
console.log('Cells:', cells.length);
console.log('Villages:', villages.length);