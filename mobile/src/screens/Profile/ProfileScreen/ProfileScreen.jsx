import React, { useEffect, useState } from 'react'
import { Avatar } from 'react-native-paper'
import {
  StyleSheet, Text, View, Image, TouchableOpacity
} from 'react-native'
import Container, { Toast } from 'toastify-react-native'
import {
  Layout, Loading, ScreenLayout
} from '../../../components'
import { COLORS, FONTS } from '../../../../constants'
import penImg from '../../../../assets/images/menu/pen.png'

const defaultImg = require('../../../../assets/images/default.png')

const img = ''

const apiUrl = 'https://ambulance.rc-respect.ru/api/'
const headers = {
  Accept: 'application/json',
  'Content-Type': 'application/json',
}
const useUserQuery = (id) => {
  const [user, setUser] = useState({ username: '', phone: '' })
  const [loading, setLoading] = useState(true)

  const showToasts = () => {
    Toast.error('Ошибка получения данных')
  }

  const getUser = async () => {
    try {
      const response = await fetch(`${apiUrl}users/${id}`, {
        headers
      })
      const json = await response.json()
      setUser(json)
    } catch (error) {
      showToasts()
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    getUser().then()
  }, [id])

  return (
    { loading, user }
  )
}

export const ProfileScreen = ({ navigation }) => {
  const { loading, user } = useUserQuery(1)
  const pattern = /(\+7|7|8)[\s(]?(\d{3})[\s)]?(\d{3})[\s-]?(\d{2})[\s-]?(\d{2})/g
  const { phone, name, position } = user
  const modifyPhone = () => phone.toString().replace(pattern, '+7 ($2) $3-$4-$5')
  return (
    <ScreenLayout>
      <Container position="top" />
      <Layout>
        {loading ? (
          <Loading />
        ) : (
          <View>
            <View style={styles.head} />
            <View style={styles.top}>
              <Avatar.Image size={120} source={img || defaultImg} style={styles.root} />
              <TouchableOpacity
                onPress={() => navigation.navigate('editProfile')}
                activeOpacity={1}
                style={styles.btn}
              >
                <Image
                  resizeMode="contain"
                  source={penImg}
                  style={styles.img}
                />
              </TouchableOpacity>
            </View>
            <Text style={styles.title}>{name}</Text>
            <Text style={styles.text}>{position}</Text>
            <View style={styles.wrap}>
              <Text style={styles.label}>Номер телефона</Text>
              <Text style={styles.info}>{modifyPhone()}</Text>
            </View>
          </View>
        )}
      </Layout>
    </ScreenLayout>
  )
}

const styles = StyleSheet.create({
  btn: {
    width: 40,
    height: 40,
    backgroundColor: COLORS.thin,
    borderRadius: 12,
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: COLORS.black,
    shadowOffset: {
      width: 0,
      height: 3,
    },
    shadowOpacity: 0.29,
    shadowRadius: 4.65,
    elevation: 7,
  },
  img: {
    width: 23,
    height: 23
  },
  top: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center'
  },
  title: {
    marginBottom: 0,
    ...FONTS.head,
  },
  text: {
    ...FONTS.text,
    lineHeight: 24,
    letterSpacing: 0.5,
    color: COLORS.gray,
  },
  root: {
    marginBottom: 16,
    backgroundColor: COLORS.transparent
  },
  head: {
    position: 'absolute',
    width: '120%',
    backgroundColor: COLORS.primary,
    left: -16,
    top: -25,
    height: 95
  },
  wrap: {
    borderColor: COLORS.primary,
    borderWidth: 1,
    borderRadius: 16,
    marginTop: 16,
    padding: 16
  },
  label: {
    ...FONTS.chatText,
    letterSpacing: 0.25,
    color: COLORS.primary,
    marginBottom: 4
  },
  info: {
    ...FONTS.text,
    color: COLORS.primary
  }
})
