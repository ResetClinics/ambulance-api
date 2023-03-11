import { useEffect, useState } from 'react'

export const useMedicineList = (items) => {
  const [medicine, setMedicine] = useState()
  useEffect(() => { setMedicine(items) }, [items])
  const addMedicine = (id) => {
    const newMedicine = medicine.map((el) => ({
      id: el.id,
      name: el.name,
      count: el.id === id ? el.count + 1 : el.count
    }))
    setMedicine(newMedicine)
  }

  const removeMedicine = (id) => {
    const newMedicine = medicine.map((el) => ({
      id: el.id,
      name: el.name,
      count: el.id === id ? el.count - 1 : el.count
    }))
    setMedicine(newMedicine)
  }

  const clearMedicine = () => {
    const newMedicine = medicine.map((el) => ({
      id: el.id,
      name: el.name,
      count: 0
    }))
    setMedicine(newMedicine)
  }
  return {
    medicine, addMedicine, removeMedicine, clearMedicine
  }
}
