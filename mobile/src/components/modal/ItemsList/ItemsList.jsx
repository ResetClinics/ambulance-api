import React from 'react'
import {
  FlatList,
  StyleSheet, Text, View
} from 'react-native'
import { Button, IconButton } from 'react-native-paper'
import { COLORS, FONTS } from '../../../../constants'
import { useMedicineList } from './useMedicineList'

const ListItem = ({
  name, id, addMedicine, count, removeMedicine
}) => (
  <View style={styles.item}>
    <Text style={styles.itemText}>{name}</Text>
    <View style={styles.wrapper}>
      {
        count > 0 && (
          <View style={styles.wrapper}>
            <IconButton
              icon="minus"
              iconColor={COLORS.primary}
              containerColor={COLORS.white}
              size={20}
              onPress={() => removeMedicine(id)}
            />
            <Text>{count}</Text>
          </View>
        )
      }
      <IconButton
        icon="plus"
        iconColor={COLORS.primary}
        containerColor={COLORS.white}
        size={20}
        onPress={() => addMedicine(id)}
      />
    </View>
  </View>
)

export const ItemsList = ({ items, onSave, closeMedicineWindow }) => {
  const {
    medicine, addMedicine, removeMedicine, clearMedicine
  } = useMedicineList(items)

  const saveMedicine = () => {
    const listMedicine = medicine.filter((el) => el.count > 0)
    onSave(listMedicine)
    closeMedicineWindow()
  }

  return (
    <View style={styles.root}>
      <FlatList
        style={styles.wrap}
        data={medicine}
        /* eslint-disable-next-line react/jsx-props-no-spreading */
        renderItem={({ item }) => (
          // eslint-disable-next-line react/jsx-props-no-spreading
          <ListItem {...item} addMedicine={addMedicine} removeMedicine={removeMedicine} />
        )}
      />
      <View style={styles.holder}>
        <Button mode="outlined" style={styles.btn} onPress={clearMedicine}>Сбросить</Button>
        <Button mode="contained" style={styles.btn} onPress={saveMedicine}>Сохранить</Button>
      </View>
    </View>
  )
}

const styles = StyleSheet.create({
  root: {
    borderRadius: 4,
    backgroundColor: COLORS.white,
    marginTop: 16,
    padding: 10,
  },
  item: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingLeft: 18,
    paddingRight: 5,
  },
  itemText: {
    ...FONTS.text,
    color: COLORS.primary
  },
  holder: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 25,
    paddingBottom: 10,
    paddingTop: 18,
    marginTop: 'auto',
    backgroundColor: COLORS.white,
    borderTopColor: COLORS.light,
    borderTopWidth: 1,
    marginHorizontal: -10
  },
  btn: {
    width: '47%'
  },
  wrap: {
    maxHeight: 300,
  },
  wrapper: {
    flexDirection: 'row',
    alignItems: 'center'
  }
})
