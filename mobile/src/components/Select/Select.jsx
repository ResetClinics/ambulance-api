import React, { useState } from 'react'
import { StyleSheet } from 'react-native'
import { List } from 'react-native-paper'
import { COLORS } from '../../../constants'

const items = [
  {
    title: 'Сегодня',
    id: 1,
  },
  {
    title: 'Вчера',
    id: 2,
  },
  {
    title: 'За неделю',
    id: 3,
  },
  {
    title: 'За месяц',
    id: 4,
  },
  {
    title: 'За год',
    id: 5,
  },
]

const Item = ({ title, active, onPress }) => {

  if (active) {
    return (
      <List.Item
        style={styles.active}
        title={title}
        onPress={onPress}
        right={() => <List.Icon color={COLORS.primary} icon="check" />}
      />
    )
  }
  return (
    <List.Item
      title={title}
      onPress={onPress}
      right={false}
    />
  )
}

export const Select = () => {
  const [activeIndex, setActiveIndex] = useState(null)
  const setActive = (index) => {
    if (activeIndex === index) {
      setActiveIndex(null)
    } else {
      setActiveIndex(index)
    }
  }

  return (
    <List.Section style={styles.select}>
      {
        items.map((item) => (
          <Item
              /* eslint-disable-next-line react/jsx-props-no-spreading */
            {...item}
            key={item.id}
            active={item.id === activeIndex}
            onPress={() => setActive(item.id)}
          />
        ))
      }
    </List.Section>
  )
}

const styles = StyleSheet.create({
  select: {
    backgroundColor: COLORS.white,
    marginTop: 40,
    marginBottom: 'auto',
    marginLeft: -5,
    width: 200,
    borderRadius: 6,
    borderColor: COLORS.gray,
    borderWidth: 1,
    overflow: 'hidden'
  },
  active: {
    backgroundColor: COLORS.secondary
  }
})
