import React, { useCallback, useEffect, useState } from 'react'
import {
  StyleSheet, View, Image, Text,
} from 'react-native'
import {
  Bubble, GiftedChat, InputToolbar, Send, Day,
} from 'react-native-gifted-chat'
import {
  BottomNavigation, Layout, ScreenLayout
} from '../../components'
import { COLORS, FONTS } from '../../../constants'
import enterImg from '../../../assets/images/enter.png'
import 'dayjs/locale/ru'
import logoImg from '../../../assets/icon.png'

export const Chat = ({ navigation }) => {
  const [messages, setMessages] = useState([])

  useEffect(() => {
    setMessages([
      {
        _id: 1,
        text: 'Завтра совещание в 19:00, всем присутствовать',
        createdAt: new Date(),
        user: {
          _id: 2,
          name: 'Натали',
          avatar: 'https://placeimg.com/140/140/any',
        },
      }
    ])
  }, [])

  const onSend = useCallback((messages = []) => {
    setMessages((previousMessages) => GiftedChat.append(previousMessages, messages))
  }, [])

  const renderBubble = (props) => (
    <View>
      <Bubble
        {...props}
        wrapperStyle={{
          left: {
            backgroundColor: COLORS.white,
            borderRadius: 4,
            paddingVertical: 8,
            paddingHorizontal: 4,
            borderTopLeftRadius: 0,
            minWidth: '70%',
            maxWidth: '70%',
            marginBottom: 10
          },
          right: {
            backgroundColor: COLORS.white,
            borderRadius: 4,
            paddingVertical: 8,
            paddingHorizontal: 4,
            borderTopRightRadius: 0,
            minWidth: '70%',
            maxWidth: '70%',
            marginBottom: 10
          },
        }}
        textStyle={{
          right: {
            ...FONTS.chatText
          },
          left: {
            ...FONTS.chatText
          }
        }}
        usernameStyle={{
          ...FONTS.smallText,
          color: COLORS.primary
        }}
        timeTextStyle={{
          left: { color: COLORS.gray },
          right: { color: COLORS.gray },
        }}
        bottomContainerStyle={{
          left: {
            justifyContent: 'space-between', marginTop: 5
          },
          right: {
            justifyContent: 'flex-end', marginTop: 1
          },
        }}
      />
    </View>
  )

  const renderSend = (props) => (
    <Send {...props}>
      <View>
        <Image
          resizeMode="contain"
          source={enterImg}
          style={styles.img}
        />
      </View>
    </Send>
  )

  const renderInputToolbar = (props) => (
    <InputToolbar
      {...props}
      containerStyle={styles.input}
      placeholderTextColor={COLORS.gray}
    />
  )
  const renderAvatar = (props) => {
    const userName = props.user.name.substr(0, 1).toUpperCase()
    if (props.user.avatar) {
      return (
        <Image
          resizeMode="contain"
          source={props.user.avatar}
          style={styles.avatarImg}
        />
      )
    }
    return (
      <View style={styles.avatar}>
        <Text style={styles.name}>{userName}</Text>
      </View>
    )
  }

  const renderDay = (props) => (
    <Day {...props} textStyle={{ ...FONTS.chatText, color: COLORS.primary }} />
  )

  return (
    <ScreenLayout>
      <Layout>
        <View style={styles.root}>
          <GiftedChat
            messages={messages}
            onSend={(messages) => onSend(messages)}
            user={{
              _id: 3,
              name: 'Петр Николаев',
              /* avatar: logoImg, */
            }}
            renderBubble={renderBubble}
            alwaysShowSend
            renderSend={renderSend}
            placeholder="Сообщение"
            locale="ru"
            showUserAvatar
            renderUsernameOnMessage
            renderInputToolbar={renderInputToolbar}
            renderAvatarOnTop
            showAvatarForEveryMessage
            renderAvatar={renderAvatar}
            renderDay={renderDay}
          />
        </View>
      </Layout>
      <BottomNavigation navigation={navigation} />
    </ScreenLayout>
  )
}

const styles = StyleSheet.create({
  root: {
    flex: 1,
    backgroundColor: COLORS.secondary,
    borderWidth: 1,
    borderColor: COLORS.primary,
    borderRadius: 4,
    overflow: 'hidden',
  },
  img: {
    width: 24,
    height: 24,
    marginRight: 16,
    marginBottom: 10
  },
  input: {
    borderTopLeftRadius: 4,
    borderTopRightRadius: 4,
    overflow: 'hidden',
    ...FONTS.text,
  },
  avatar: {
    width: 24,
    height: 24,
    borderRadius: 24,
    backgroundColor: COLORS.primary,
    justifyContent: 'center',
    alignItems: 'center',
  },
  name: {
    ...FONTS.smallText,
    color: COLORS.white,
  },
  avatarImg: {
    width: 24,
    height: 24,
    borderRadius: 24,
  }
})
